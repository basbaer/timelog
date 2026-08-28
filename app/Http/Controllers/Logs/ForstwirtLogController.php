<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Logs\BaseLogController;
use App\Models\ForstwirtLog;
use App\Services\ForstwirtLogService;
use App\Services\ProjectService;
use Illuminate\Support\Collection;
use App\Services\WorkerLogService;
use App\Http\Requests\StoreForstwirtLogRequest;
use Illuminate\Http\JsonResponse;

class ForstwirtLogController extends BaseLogController
{

    public function __construct(
        WorkerLogService $workerLogService,
        ProjectService $projectService,
    )
    {
        parent::__construct($workerLogService, $projectService);
    }

    public function logModel(): string
    {
        return ForstwirtLog::class;
    }

    public function logService(): string
    {
        return ForstwirtLogService::class;
    }

    public function route(): string
    {
        return 'log.forstwirt';
    }

    public function viewPrefix(): string
    {
        return 'log-forstwirt';
    }

    protected function addPreviousData(int $worker_id, Collection $projects): Collection
    {
        return $projects;
    }

    public function store(StoreForstwirtLogRequest $request): JsonResponse
    {
        return parent::storeLog($request);
    }
     
    public function edit(int $worker_id, int $log_id): JsonResponse
    {
        return parent::editLog($worker_id, $log_id);
    }

    public function update(StoreForstwirtLogRequest $request): JsonResponse
    {
        return parent::storeLog($request);
    }


    
}
