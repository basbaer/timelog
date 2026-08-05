<?php

namespace App\Http\Controllers\Logs;

use App\Http\Requests\StoreHarvesterLogRequest;
use App\Models\ForstwirtWorkingType;
use App\Models\HarvesterLog;
use App\Models\User;
use App\Services\ForstwirtLogService;
use App\Services\WorkerLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class HarvesterLogController extends BaseLogController
{
    public function __construct(
        WorkerLogService $workerLogService,
        private readonly ForstwirtLogService $forstwirtLogService,
    ) {
        parent::__construct($workerLogService);
    }

    public function logModel(): string
    {
        return HarvesterLog::class;
    }

    public function workingTypeModel(): string
    {
        return ForstwirtWorkingType::class;
    }

    public function route(): string
    {
        return 'log.harvester';
    }

    public function viewPrefix(): string
    {
        return 'log-harvester';
    }

    protected function addPreviousData(int $user_id, Collection $projects): Collection
    {
        foreach ($projects as $project) {
            $lastLog = $this->logModel()::where('user_id', $user_id)
                ->where('project_id', $project->id)
                ->latest()
                ->first();

            $project->last_fm_total = $lastLog ? $lastLog->fm_total : 0;
            $project->last_bs = $lastLog ? $lastLog->bs_to : 0;

            $projects[$project->id] = $project;
        }

        return $projects;
    }

    protected function mapValidatedToLogs(array $validated): array
    {
        $logDate = $validated['log_date'];

        return collect($validated['work_logs'] ?? [])
            ->map(function (array $workLog, $projectId) use ($logDate) {
                return [
                    'project_id' => (int) $projectId,
                    'date' => $logDate,
                    'has_harvester_payload' => $this->hasHarvesterPayload($workLog),
                    'start' => $workLog['start'] ?? null,
                    'end' => $workLog['end'] ?? null,
                    'sum' => $workLog['sum'] ?? null,
                    'pause' => isset($workLog['pause']) ? (int) $workLog['pause'] : 0,
                    'bs_start' => isset($workLog['bs_start']) ? (float) $workLog['bs_start'] : null,
                    'bs_end' => isset($workLog['bs_end']) ? (float) $workLog['bs_end'] : null,
                    'bs_diff' => $workLog['bs_diff'] ?? null,
                    'stueckzahl' => isset($workLog['stueckzahl']) ? (float) $workLog['stueckzahl'] : null,
                    'fm_gesamt' => isset($workLog['fm_gesamt']) ? (float) $workLog['fm_gesamt'] : null,
                    'fm_day' => $workLog['fm_day'] ?? null,
                    'forstwirt_work_entries' => collect($workLog['entries'] ?? [])
                        ->map(fn(array $entry) => [
                            'project_id' => (int) $projectId,
                            'date' => $logDate,
                            'type' => $entry['type'],
                            'start' => $entry['start'],
                            'end' => $entry['end'],
                            'pause' => isset($entry['pause']) ? (int) $entry['pause'] : 0,
                            'sum' => $entry['sum'] ?? null,
                            'comment' => $entry['comment'] ?? null,
                        ])
                        ->values()
                        ->all(),
                ];
            })
            ->values()
            ->all();
    }

    public function store(StoreHarvesterLogRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $mappedLogs = $this->mapValidatedToLogs($validated);
        $user = User::findOrFail((int) $request->input('user_id'));

        $lastLog = null;

        foreach ($mappedLogs as $logData) {
            if ($logData['has_harvester_payload']) {
                $lastLog = $this->workerLogService->saveLogs($user, [$logData]) ?? $lastLog;
            }

            if (!empty($logData['forstwirt_work_entries'])) {
                $forstwirtLastLog = $this->forstwirtLogService->saveLogs($logData['forstwirt_work_entries'], $user->id);
                $lastLog = $forstwirtLastLog ?? $lastLog;
            }
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.worker.show', ['worker_id' => (int) $lastLog->user_id]);
        }

        return redirect()->route($this->route() . '.success', ['worker_id' => (int) $lastLog->user_id]);
    }

    private function hasHarvesterPayload(array $workLog): bool
    {
        foreach (['start', 'end', 'sum', 'pause', 'bs_start', 'bs_end', 'bs_diff', 'stueckzahl', 'fm_gesamt', 'fm_day'] as $field) {
            if (array_key_exists($field, $workLog) && trim((string) ($workLog[$field] ?? '')) !== '') {
                return true;
            }
        }

        return false;
    }
}
