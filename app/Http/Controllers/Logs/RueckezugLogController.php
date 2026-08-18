<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Logs\BaseLogController;
use App\Http\Requests\StoreRueckezugLogRequest;
use App\Models\ForstwirtWorkingType;
use App\Models\RueckezugLog;
use App\Models\User;
use App\Services\ForstwirtLogService;
use App\Services\RueckezugLogService;
use App\Services\ProjectService;
use App\Services\WorkerLogService;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class RueckezugLogController extends BaseLogController
{
    public function __construct(
        WorkerLogService $workerLogService,
        private readonly ForstwirtLogService $forstwirtLogService,
        private readonly RueckezugLogService $rueckezugLogService,
        private readonly ProjectService $projectService,
    ) {
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
            

            $project->last_bs = $this->rueckezugLogService->getLastBsTo($user_id, $project->id);
            $project->last_average_distance = $this->rueckezugLogService->getLastAverageDistance($user_id, $project->id);

            $projects[$project->id] = $project;
        }

        return $projects;
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

    protected function mapValidatedToLogs(array $validated): array
    {
        $logDate = $validated['log_date'];

        return collect($validated['work_logs'] ?? [])
            ->map(function (array $workLog, $projectId) use ($logDate) {
                return [
                    'project_id' => (int) $projectId,
                    'date' => $logDate,
                    'has_rueckezug_payload' => $this->hasRueckezugPayload($workLog),
                    'start' => $workLog['start'] ?? null,
                    'end' => $workLog['end'] ?? null,
                    'sum' => $this->getSumForMainLog($workLog),
                    'pause' => isset($workLog['pause']) ? (int) $workLog['pause'] : 0,
                    'bs_start' => isset($workLog['bs_start']) ? (float) $workLog['bs_start'] : null,
                    'bs_end' => isset($workLog['bs_end']) ? (float) $workLog['bs_end'] : null,
                    'bs_diff' => $workLog['bs_diff'] ?? null,
                    'loadings' => isset($workLog['loadings']) ? (float) $workLog['loadings'] : null,
                    'average_distance' => isset($workLog['average_distance']) ? (float) $workLog['average_distance'] : null,
                    'forstwirt_work_entries' => collect($workLog['entries'] ?? [])
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
        $editLogId = $request->integer('edit_log_id');

        if ($editLogId) {
            $originalDate = $request->input('edit_log_date', $validated['log_date']);
            $this->workerLogService->deleteLogsFrom($user, $originalDate);
        }

        $lastLog = null;

        foreach ($mappedLogs as $logData) {
            if ($logData['has_rueckezug_payload']) {
                $lastLog = $this->rueckezugLogService->saveLogs([$logData], $user->id) ?? $lastLog;
            }

            if (!empty($logData['forstwirt_work_entries'])) {
                $forstwirtLastLog = $this->forstwirtLogService->saveLogs($logData['forstwirt_work_entries'], $user->id);
                $lastLog = $forstwirtLastLog ?? $lastLog;
            }
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.worker.show', ['worker_id' => (int) $lastLog->user_id]);
        }

        return redirect()->route($this->route() . '.success', ['worker_id' => (int) $lastLog->user_id]);
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

    private function hasRueckezugPayload(array $workLog): bool
    {
        foreach (['start', 'end', 'sum', 'pause', 'bs_start', 'bs_end', 'bs_diff', 'loadings', 'average_distance'] as $field) {
            if (array_key_exists($field, $workLog) && trim((string) ($workLog[$field] ?? '')) !== '') {
                return true;
            }
        }

        return false;
    }
}
