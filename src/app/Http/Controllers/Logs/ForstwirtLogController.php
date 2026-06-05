<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Logs\BaseLogController;
use App\Http\Requests\StoreForstwirtLogRequest;
use App\Models\ForstwirtLog;
use App\Models\ForstwirtWorkingType;
use App\Services\ForstwirtLogService;
use App\Services\WorkerLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;

class ForstwirtLogController extends BaseLogController
{
    private ForstwirtLogService $forstwirtLogService;

    public function __construct(WorkerLogService $workerLogService, ForstwirtLogService $forstwirtLogService)
    {
        parent::__construct($workerLogService);
        $this->forstwirtLogService = $forstwirtLogService;
    }

    public function logModel(): string
    {
        return ForstwirtLog::class;
    }

    public function workingTypeModel(): string
    {
        return ForstwirtWorkingType::class;
    }

    public function route(): string
    {
        return 'log.forstwirt';
    }

    public function viewPrefix(): string
    {
        return 'log-forstwirt';
    }

    protected function addPreviousData(int $user_id, Collection $projects): Collection
    {
        return $projects;
    }

    protected function mapValidatedToLogs(array $validated): array
    {
        $logDate = $validated['log_date'];

        return collect($validated['work_logs'] ?? [])
            ->flatMap(function (array $projectWorkLogs, $projectId) use ($logDate) {
                return collect($projectWorkLogs)
                    ->filter(fn(array $entry) => trim((string) ($entry['type'] ?? '')) !== '')
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
                    ->values();
            })
            ->values()
            ->all();
    }

    public function store(StoreForstwirtLogRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $mappedLogs = $this->mapValidatedToLogs($validated);

        $lastLog = $this->forstwirtLogService->saveLogs($mappedLogs, $request->input('user_id'));

        return redirect()->route($this->route() . '.success', ['worker_id' => (int) $lastLog->user_id]);
    }


    
}
