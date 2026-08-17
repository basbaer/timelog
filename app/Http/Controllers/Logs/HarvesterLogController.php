<?php

namespace App\Http\Controllers\Logs;

use App\Http\Requests\StoreHarvesterLogRequest;
use App\Models\ForstwirtWorkingType;
use App\Models\HarvesterLog;
use App\Models\User;
use App\Services\ForstwirtLogService;
use App\Services\HarvesterLogService;
use App\Services\ProjectService;
use App\Services\WorkerLogService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class HarvesterLogController extends BaseLogController
{
    public function __construct(
        WorkerLogService $workerLogService,
        private readonly ForstwirtLogService $forstwirtLogService,
        private readonly HarvesterLogService $harvesterLogService,
        private readonly ProjectService $projectService,
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

    public function edit(int $user_id, int $log_id)
    {
        $user = User::findOrFail($user_id);
        $log = HarvesterLog::with('project')->where('user_id', $user->id)->findOrFail($log_id);
        $editDate = Carbon::parse($log->date)->toDateString();
        $projects = $this->projectService->getOpenProjects($user->id, $editDate, $editDate);

        if (! $projects->contains('id', $log->project_id)) {
            $projects->push($this->projectService->getProjectById($log->project_id));
        }

        $projects = $projects->sortBy('title')->values();

        return view('log-forms/log-harvester-edit', [
            'name' => $user->first_name . ' ' . $user->last_name,
            'user_id' => $user->id,
            'log' => $log,
            'projects' => $projects,
            'isAdmin' => (function () {
                /** @var \App\Models\User $currentUser */
                $currentUser = Auth::user();

                return $currentUser->isAdmin();
            })(),
        ]);
    }

    protected function buildEditPrefill(Collection $logs, string $date): array
    {
        $prefill = [
            'log_date' => $date,
            'work_logs' => [],
        ];

        foreach ($logs as $log) {
            $projectId = (int) $log->project_id;
            $entryLabel = $log->entry_label ?? null;

            if ($entryLabel === 'harvester') {
                $prefill['work_logs'][$projectId] = [
                    'start' => $log->start ? Carbon::parse($log->start)->format('H:i') : null,
                    'end' => $log->end ? Carbon::parse($log->end)->format('H:i') : null,
                    'pause' => $log->pause ?? 0,
                    'sum' => $log->sum ? Carbon::parse($log->sum)->format('H:i') : null,
                    'bs_start' => $log->bs_from,
                    'bs_end' => $log->bs_to,
                    'bs_diff' => $log->bs_diff,
                    'stueckzahl' => $log->fm_amount,
                    'fm_gesamt' => $log->fm_total,
                    'fm_day' => $log->fm_day,
                ];
            }

            if ($entryLabel === 'forstwirt') {
                $prefill['work_logs'][$projectId]['entries'] ??= [];
                $prefill['work_logs'][$projectId]['entries'][] = [
                    'type' => $log->workingType?->slug ?? '',
                    'start' => $log->start ? Carbon::parse($log->start)->format('H:i') : null,
                    'end' => $log->end ? Carbon::parse($log->end)->format('H:i') : null,
                    'pause' => $log->pause ?? 0,
                    'sum' => $log->sum ? Carbon::parse($log->sum)->format('H:i') : null,
                    'comment' => $log->comment,
                ];
            }
        }

        return $prefill;
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
                    'sum' => $this->getSumForMainLog($workLog),
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
        $editLogId = $request->integer('edit_log_id');

        if ($editLogId) {
            $originalDate = $request->input('edit_log_date', $validated['log_date']);
            $this->workerLogService->deleteLogsFrom($user, $originalDate);
        }

        $lastLog = null;

        foreach ($mappedLogs as $logData) {
            if ($logData['has_harvester_payload']) {
                $lastLog = $this->harvesterLogService->saveLogs([$logData], $user->id) ?? $lastLog;
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

    public function update(int $user_id, int $log_id): RedirectResponse
    {
        $validated = request()->validate([
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'date' => ['required', 'date'],
            'start' => ['nullable', 'date_format:H:i'],
            'end' => ['nullable', 'date_format:H:i'],
            'pause' => ['nullable', 'integer', 'min:0'],
            'sum' => ['nullable', 'date_format:H:i'],
            'bs_start' => ['nullable', 'numeric', 'min:0'],
            'bs_end' => ['nullable', 'numeric', 'min:0'],
            'bs_diff' => ['nullable', 'string'],
            'stueckzahl' => ['nullable', 'numeric', 'min:0'],
            'fm_gesamt' => ['nullable', 'numeric', 'min:0'],
            'fm_day' => ['nullable', 'numeric'],
        ]);

        $user = User::findOrFail($user_id);
        $log = HarvesterLog::where('user_id', $user->id)->findOrFail($log_id);

        $log->project_id = (int) $validated['project_id'];
        $log->date = $validated['date'];
        $log->start = $validated['start'] ?? null;
        $log->end = $validated['end'] ?? null;
        $log->pause = isset($validated['pause']) ? (int) $validated['pause'] : 0;
        $log->sum = $validated['sum'] ?? null;
        $log->bs_from = isset($validated['bs_start']) ? (float) $validated['bs_start'] : null;
        $log->bs_to = isset($validated['bs_end']) ? (float) $validated['bs_end'] : null;
        $log->bs_diff = $validated['bs_diff'] ?? null;
        $log->fm_amount = isset($validated['stueckzahl']) ? (float) $validated['stueckzahl'] : null;
        $log->fm_total = isset($validated['fm_gesamt']) ? (float) $validated['fm_gesamt'] : null;
        $log->fm_day = isset($validated['fm_day']) ? (float) $validated['fm_day'] : null;
        $log->save();

        return redirect()->route('worker.show', ['worker_id' => $user->id])->with('success', 'Eintrag erfolgreich aktualisiert.');
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
