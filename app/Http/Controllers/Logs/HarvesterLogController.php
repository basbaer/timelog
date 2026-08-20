<?php

namespace App\Http\Controllers\Logs;

use App\Http\Requests\StoreHarvesterLogRequest;
use App\Models\HarvesterLog;
use App\Models\User;
use App\Services\HarvesterLogService;
use App\Services\WorkerLogService;
use App\Services\ProjectService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class HarvesterLogController extends BaseLogController
{
    public function __construct(
        WorkerLogService $workerLogService,
        private readonly HarvesterLogService $harvesterLogService,
        private readonly ProjectService $projectService,
    ) {
        parent::__construct($workerLogService);
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

    public function viewPrefix(): string
    {
        return 'log-harvester';
    }

    protected function addPreviousData(int $user_id, Collection $projects): Collection
    {
        foreach ($projects as $project) {
        
            $project->last_fm_total = $this->harvesterLogService->getLastFmTotal($user_id, $project->id);
            $project->last_bs = $this->harvesterLogService->getLastBsTo($user_id, $project->id);
            $project->last_fm_amount = $this->harvesterLogService->getLastFmAmount($user_id, $project->id);

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

    public function store(StoreHarvesterLogRequest $request): JsonResponse
    {
        return $this->storeLog($request);
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
}
