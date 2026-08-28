<?php

namespace App\Http\Controllers\Logs;

use App\Http\Requests\StoreHarvesterLogRequest;
use App\Models\HarvesterLog;
use App\Services\HarvesterLogService;
use App\Services\WorkerLogService;
use App\Services\ProjectService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class HarvesterLogController extends BaseLogController
{
    public function __construct(
        WorkerLogService $workerLogService,
        private readonly HarvesterLogService $harvesterLogService,
        ProjectService $projectService,
    ) {
        parent::__construct($workerLogService, $projectService);
    }

    public function logModel(): string
    {
        return HarvesterLog::class;
    }

    public function logService(): string
    {
        return HarvesterLogService::class;
    }

    public function route(): string
    {
        return 'log.harvester';
    }
    
    protected function addPreviousData(int $worker_id, Collection $projects): Collection
    {
        foreach ($projects as $project) {

            $project->last_fm_total = $this->harvesterLogService->getLastFmTotal($worker_id, $project->id);
            $project->last_bs = $this->harvesterLogService->getLastBsTo($worker_id, $project->id);
            $project->last_fm_amount = $this->harvesterLogService->getLastFmAmount($worker_id, $project->id);

            $projects[$project->id] = $project;
        }

        return $projects;
    }

    public function store(StoreHarvesterLogRequest $request): JsonResponse
    {
        return parent::storeLog($request);
    }

    public function edit(int $worker_id, int $log_id): JsonResponse
    {
        return parent::editLog($worker_id, $log_id);
    }

    public function update(StoreHarvesterLogRequest $request): JsonResponse
    {
        return parent::storeLog($request);
    }
}
