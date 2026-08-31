<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Logs\BaseLogController;
use App\Models\RueckezugLog;
use App\Services\RueckezugLogService;
use App\Services\ProjectService;
use App\Services\WorkerLogService;
use Illuminate\Support\Collection;
use App\Http\Requests\StoreRueckezugLogRequest;
use Illuminate\Http\JsonResponse;

class RueckezugLogController extends BaseLogController
{
    private readonly RueckezugLogService $rueckezugLogService;

    public function __construct(
        WorkerLogService $workerLogService,
        RueckezugLogService $rueckezugLogService,
        ProjectService $projectService,
    ) {
        parent::__construct($workerLogService, $projectService);
        $this->rueckezugLogService = $rueckezugLogService;
    }

    protected function logModel(): string
    {
        return RueckezugLog::class;
    }

    protected function logService(): string
    {
        return RueckezugLogService::class;
    }

    protected function route(): string
    {
        return 'log.rueckezug';
    }

    protected function addPreviousData(int $worker_id, Collection $projects): Collection
    {
        foreach ($projects as $project) {

            $project->last_bs = $this->rueckezugLogService->getLastBsTo($worker_id, $project->id);
            $project->last_average_distance = $this->rueckezugLogService->getLastAverageDistance($worker_id, $project->id);

            $projects[$project->id] = $project;
        }

        return $projects;
    }

    public function store(StoreRueckezugLogRequest $request): JsonResponse
    {
        return $this->storeLog($request);
    }

    public function edit(int $worker_id, int $log_id): JsonResponse
    {
        return parent::editLog($worker_id, $log_id);
    }

    public function update(StoreRueckezugLogRequest $request): JsonResponse
    {
        return parent::storeLog($request);
    }

}
