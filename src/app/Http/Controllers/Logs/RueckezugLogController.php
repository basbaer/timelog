<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Logs\BaseLogController;
use App\Http\Requests\StoreRueckezugLogRequest;
use App\Models\ForstwirtWorkingType;
use App\Models\RueckezugLog;
use App\Models\User;
use App\Services\WorkerLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;

class RueckezugLogController extends BaseLogController
{
    public function __construct(WorkerLogService $workerLogService)
    {
        parent::__construct($workerLogService);
    }

    protected function logModel(): string
    {
        return RueckezugLog::class;
    }

    protected function workingTypeModel(): string
    {
        return ForstwirtWorkingType::class;
    }

    protected function route(): string
    {
        return 'log.rueckezug';
    }

    protected function viewPrefix(): string
    {
        return 'log-rueckezug';
    }

    protected function addPreviousData(int $user_id, Collection $projects): Collection
    {
        foreach ($projects as $project) {
            $lastLog = $this->logModel()::where('user_id', $user_id)
                ->where('project_id', $project->id)
                ->latest()
                ->first();

            $project->last_bs = $lastLog ? $lastLog->bs_to : 0;
            $project->last_average_distance = $lastLog ? $lastLog->averarge_distance : 0;

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
                    'start' => $workLog['start'] ?? null,
                    'end' => $workLog['end'] ?? null,
                    'sum' => $workLog['sum'] ?? null,
                    'bs_start' => isset($workLog['bs_start']) ? (float) $workLog['bs_start'] : null,
                    'bs_end' => isset($workLog['bs_end']) ? (float) $workLog['bs_end'] : null,
                    'bs_diff' => $workLog['bs_diff'] ?? null,
                    'loadings' => isset($workLog['loadings']) ? (float) $workLog['loadings'] : null,
                    'average_distance' => isset($workLog['average_distance']) ? (float) $workLog['average_distance'] : null,
                    'forstwirt_work_entries' => collect($workLog['entries'] ?? [])
                        ->map(fn (array $entry) => [
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

    public function store(StoreRueckezugLogRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $mappedLogs = $this->mapValidatedToLogs($validated);
        $user = User::findOrFail((int) $request->input('user_id'));
        $lastLog = $this->workerLogService->saveLogs($user, $mappedLogs);

        return redirect()->route($this->route() . '.success', ['worker_id' => (int) $lastLog->user_id]);
    }


    
}
