<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Logs\BaseLogController;
use App\Http\Requests\StoreRueckezugLogRequest;
use App\Models\ForstwirtLog;
use App\Models\ForstwirtWorkingType;
use App\Models\RueckezugLog;
use App\Services\ForstwirtLogService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Collection;

class RueckezugLogController extends BaseLogController
{
    public function __construct(
        private ForstwirtLogService $forstwirtLogService
    ) {}

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
            $lastLog = $this->logModel()::where('user_id', $user_id)
                ->where('project_id', $project->id)
                ->latest()
                ->first();

            $project->last_bs = $lastLog ? $lastLog->bs_to : 0;
            $project->last_average_distance = $lastLog ? $lastLog->averarge_distance : 0;

            $projects[$project->id] = $project;
        }

        return $projects;
    }

    protected function mapValidatedToLogs(array $validated): array
    {
        $logDate = $validated['log_date'];

        return collect($validated['work_logs'] ?? [])
            ->map(function (array $workLog, $projectId) use ($logDate) {
                return [
                    'project_id' => (int) $projectId,
                    'date' => $logDate,
                    'start' => $workLog['start'] ?? null,
                    'end' => $workLog['end'] ?? null,
                    'sum' => $workLog['sum'] ?? null,
                    'bs_start' => isset($workLog['bs_start']) ? (float) $workLog['bs_start'] : null,
                    'bs_end' => isset($workLog['bs_end']) ? (float) $workLog['bs_end'] : null,
                    'bs_diff' => $workLog['bs_diff'] ?? null,
                    'loadings' => isset($workLog['loadings']) ? (float) $workLog['loadings'] : null,
                    'average_distance' => isset($workLog['average_distance']) ? (float) $workLog['average_distance'] : null,
                    'forstwirt_work_entries' => collect($workLog['entries'] ?? [])
                        ->map(fn (array $entry) => [
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
        $lastLog = null;

        foreach ($mappedLogs as $logData) {
            $log = new RueckezugLog();
            $log->user_id = $request->input('user_id');
            $log->project_id = $logData['project_id'];
            $log->date = $logData['date'];
            $log->start = $logData['start'];
            $log->end = $logData['end'];
            $log->sum = $logData['sum'];
            $log->bs_from = $logData['bs_start'] ?? null;
            $log->bs_to = $logData['bs_end'] ?? null;
            $log->bs_diff = $logData['bs_diff'] ?? null;
            $log->loadings = $logData['loadings'] ?? null;
            $log->averarge_distance = $logData['average_distance'] ?? null;
            $log->save();
            $lastLog = $log;

            if (!empty($logData['forstwirt_work_entries'])) {
                $this->forstwirtLogService->saveLogs($logData['forstwirt_work_entries'], (int) $log->user_id);
            }
        }

        return redirect()->route($this->route() . '.success', ['worker_id' => (int) $lastLog->user_id]);
    }

    protected function loadSuccessLogs(int $userId, string $date): Collection
    {
        $rueckezugLogs = RueckezugLog::with(['project'])
            ->where('user_id', $userId)
            ->whereDate('date', $date)
            ->get()
            ->map(function (RueckezugLog $log) {
                $log->entry_label = 'rueckezug';
                return $log;
            });

        $forstwirtLogs = ForstwirtLog::with(['project', 'workingType'])
            ->where('user_id', $userId)
            ->whereDate('date', $date)
            ->get()
            ->map(function (ForstwirtLog $log) {
                $log->entry_label = 'forstwirt';
                return $log;
            });

        return $rueckezugLogs
            ->concat($forstwirtLogs)
            ->sortBy(function ($log) {
                return sprintf('%s|%s|%s', $log->project_id, $log->start ?? '', $log->created_at ?? '');
            })
            ->values();
    }

    public function getLogOfToday(int $user_id)
    {
        $rueckezugLog = $this->logModel()::where('user_id', $user_id)->whereDate('date', today())->first();

        if (!$rueckezugLog) {
            $log = ForstwirtLog::where('user_id', $user_id)->whereDate('date', today())->first();
        }

        return $rueckezugLog ?? $log;
    }

    public function deleteLogsOfDate(int $user_id, string $date)
    {
        $rueckezugLogs = RueckezugLog::where('user_id', $user_id)->whereDate('date', $date)->get();

        foreach ($rueckezugLogs as $log) {
            $log->delete();
        }

        $this->forstwirtLogService->deleteLogsFrom($user_id, $date);
    }
}
