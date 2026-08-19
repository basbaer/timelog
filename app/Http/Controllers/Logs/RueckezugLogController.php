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
use Illuminate\Support\Facades\Auth;
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

    protected function addPreviousData(int $user_id, Collection $projects): Collection
    {
        foreach ($projects as $project) {

            $project->last_bs = $this->rueckezugLogService->getLastBsTo($user_id, $project->id);
            $project->last_average_distance = $this->rueckezugLogService->getLastAverageDistance($user_id, $project->id);

            $projects[$project->id] = $project;
        }

        return $projects;
    }

    public function store(StoreRueckezugLogRequest $request): JsonResponse
    {
        return $this->storeLog($request);
    }

    public function edit(int $user_id, int $log_id)
    {
        $user = User::findOrFail($user_id);
        $log = RueckezugLog::with('project')->where('user_id', $user->id)->findOrFail($log_id);
        $editDate = Carbon::parse($log->date)->toDateString();
        $projects = $this->projectService->getOpenProjects($user->id, $editDate, $editDate);

        if (! $projects->contains('id', $log->project_id)) {
            $projects->push($this->projectService->getProjectById($log->project_id));
        }

        $projects = $projects->sortBy('title')->values();

        return view('log-forms/log-rueckezug-edit', [
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

            if ($entryLabel === 'rueckezug') {
                $prefill['work_logs'][$projectId] = [
                    'start' => $log->start ? Carbon::parse($log->start)->format('H:i') : null,
                    'end' => $log->end ? Carbon::parse($log->end)->format('H:i') : null,
                    'pause' => $log->pause ?? 0,
                    'sum' => $log->sum ? Carbon::parse($log->sum)->format('H:i') : null,
                    'bs_start' => $log->bs_from,
                    'bs_end' => $log->bs_to,
                    'bs_diff' => $log->bs_diff,
                    'loadings' => $log->loadings,
                    'average_distance' => $log->average_distance,
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
            'loadings' => ['nullable', 'numeric', 'min:0'],
            'average_distance' => ['nullable', 'numeric', 'min:0'],
        ]);

        $user = User::findOrFail($user_id);
        $log = RueckezugLog::where('user_id', $user->id)->findOrFail($log_id);

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

        return redirect()->route('worker.show', ['worker_id' => $user->id])->with('success', 'Eintrag erfolgreich aktualisiert.');
    }
}
