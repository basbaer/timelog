<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Logs\BaseLogController;
use App\Models\ForstwirtLog;
use App\Models\ForstwirtWorkingType;
use App\Models\User;
use App\Services\ForstwirtLogService;
use App\Services\ProjectService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Services\WorkerLogService;

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

    protected function addPreviousData(int $user_id, Collection $projects): Collection
    {
        return $projects;
    }
/*
    public function store(StoreForstwirtLogRequest $request): JsonResponse
    {
        return parent::store($request);
    }
        */

    public function edit(int $worker_id, int $log_id)
    {
        $worker = User::findOrFail($worker_id);
        $log = ForstwirtLog::with(['project', 'workingType'])->where('user_id', $worker->id)->findOrFail($log_id);
        $editDate = Carbon::parse($log->date)->toDateString();
        $projects = $this->projectService->getOpenProjects($worker->id, $editDate, $editDate);

        if (! $projects->contains('id', $log->project_id)) {
            $projects->push($this->projectService->getProjectById($log->project_id));
        }

        $projects = $projects->sortBy('title')->values();
        $workTypes = ForstwirtWorkingType::query()->orderBy('slug')->get(['id', 'slug', 'name']);

        return view('log-forms/log-forstwirt-edit', [
            'name' => $worker->first_name . ' ' . $worker->last_name,
            'worker_id' => $worker->id,
            'log' => $log,
            'projects' => $projects,
            'workTypes' => $workTypes,
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
            if (($log->entry_label ?? null) !== 'forstwirt') {
                continue;
            }

            $projectId = (int) $log->project_id;
            $prefill['work_logs'][$projectId] ??= [];
            $prefill['work_logs'][$projectId][] = [
                'type' => $log->workingType?->slug ?? '',
                'start' => $log->start ? Carbon::parse($log->start)->format('H:i') : null,
                'end' => $log->end ? Carbon::parse($log->end)->format('H:i') : null,
                'pause' => $log->pause ?? 0,
                'sum' => $log->sum ? Carbon::parse($log->sum)->format('H:i') : null,
                'comment' => $log->comment,
            ];
        }

        return $prefill;
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
