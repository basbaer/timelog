<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Logs\BaseLogController;
use App\Models\RueckezugLog;
use App\Models\User;
use App\Services\RueckezugLogService;
use App\Services\ProjectService;
use App\Services\WorkerLogService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
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

    protected function viewPrefix(): string
    {
        return 'log-rueckezug';
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

    public function update(int $worker_id, int $log_id): RedirectResponse
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
            'loadings' => ['nullable', 'numeric', 'min:0'],
            'average_distance' => ['nullable', 'numeric', 'min:0'],
        ]);

        $worker = User::findOrFail($worker_id);
        $log = RueckezugLog::where('user_id', $worker->id)->findOrFail($log_id);

        $log->project_id = (int) $validated['project_id'];
        $log->date = $validated['date'];
        $log->start = $validated['start'] ?? null;
        $log->end = $validated['end'] ?? null;
        $log->pause = isset($validated['pause']) ? (int) $validated['pause'] : 0;
        $log->sum = $validated['sum'] ?? null;
        $log->bs_from = isset($validated['bs_start']) ? (float) $validated['bs_start'] : null;
        $log->bs_to = isset($validated['bs_end']) ? (float) $validated['bs_end'] : null;
        $log->bs_diff = $validated['bs_diff'] ?? null;
        $log->loadings = isset($validated['loadings']) ? (float) $validated['loadings'] : null;
        $log->average_distance = isset($validated['average_distance']) ? (float) $validated['average_distance'] : null;
        $log->save();

        return redirect()->route('worker.show', ['worker_id' => $worker->id])->with('success', 'Eintrag erfolgreich aktualisiert.');
    }
}
