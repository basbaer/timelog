<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Logs\BaseLogController;
use App\Models\ForstwirtLog;
use App\Models\User;
use App\Services\ForstwirtLogService;
use App\Services\ProjectService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
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

    public function update(int $user_id, int $log_id): RedirectResponse
    {
        $validated = request()->validate([
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'working_type_id' => ['required', 'integer', 'exists:forstwirt_working_types,id'],
            'date' => ['required', 'date'],
            'start' => ['required', 'date_format:H:i'],
            'end' => ['required', 'date_format:H:i'],
            'pause' => ['nullable', 'integer', 'min:0'],
            'sum' => ['nullable', 'date_format:H:i'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $user = User::findOrFail($user_id);
        $log = ForstwirtLog::where('user_id', $user->id)->findOrFail($log_id);

        $log->project_id = (int) $validated['project_id'];
        $log->working_type_id = (int) $validated['working_type_id'];
        $log->date = $validated['date'];
        $log->start = $validated['start'];
        $log->end = $validated['end'];
        $log->pause = isset($validated['pause']) ? (int) $validated['pause'] : 0;
        $log->sum = $validated['sum'] ?? null;
        $log->comment = $validated['comment'] ?? null;
        $log->save();

        return redirect()->route('worker.show', ['worker_id' => $user->id])->with('success', 'Eintrag erfolgreich aktualisiert.');
    }


    
}
