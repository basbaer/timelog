<?php

namespace App\Http\Controllers\Logs;

use App\Http\Requests\StoreHarvesterLogRequest;
use App\Models\ForstwirtLog;
use App\Models\ForstwirtWorkingType;
use App\Models\HarvesterLog;
use Illuminate\Support\Collection;
use Illuminate\Http\RedirectResponse;
use App\Services\ForstwirtLogService;

class HarvesterLogController extends BaseLogController
{
    public function __construct(
        private ForstwirtLogService $forstwirtLogService
    ) {}
    public function logModel(): string
    {
        return HarvesterLog::class;
    }

    public function workingTypeModel(): string
    {
        return ForstwirtWorkingType::class;
    }

    public function route(): string
    {
        return 'log.harvester';
    }

    protected function loadSuccessLogs(int $userId, string $date): Collection
    {
        $harvesterLogs = HarvesterLog::with(['project'])
            ->where('user_id', $userId)
            ->whereDate('date', $date)
            ->get()
            ->map(function (HarvesterLog $log) {
                $log->entry_label = 'harvester';
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

        return $harvesterLogs
            ->concat($forstwirtLogs)
            ->sortBy(function ($log) {
                return sprintf('%s|%s|%s', $log->project_id, $log->start ?? '', $log->created_at ?? '');
            })
            ->values();
    }

    public function viewPrefix(): string
    {
        return 'log-harvester';
    }

    protected function addPreviousData(int $user_id, Collection $projects): Collection
    {
 
        foreach ($projects as $project) {

            $lastLog = $this->logModel()::where('user_id', $user_id)
                ->where('project_id', $project->id)
                ->latest()
                ->first();

            // Add last fm_total to the project collection for use in the form
            $project->last_fm_total = $lastLog ? $lastLog->fm_total : 0;
            $project->last_bs = $lastLog ? $lastLog->bs_to : 0;

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
                    'bs_start' => isset($workLog['bs_start']) ? (int) $workLog['bs_start'] : null,
                    'bs_end' => isset($workLog['bs_end']) ? (int) $workLog['bs_end'] : null,
                    'bs_diff' => $workLog['bs_diff'] ?? null,
                    'stueckzahl' => isset($workLog['stueckzahl']) ? (int) $workLog['stueckzahl'] : null,
                    'fm_gesamt' => isset($workLog['fm_gesamt']) ? $workLog['fm_gesamt'] : null,
                    'fm_day' => $workLog['fm_day'] ?? null,
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

    public function store(StoreHarvesterLogRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $mappedLogs = $this->mapValidatedToLogs($validated);
        $lastLog = null;

        foreach ($mappedLogs as $logData) {
            $log = new HarvesterLog();
            $log->user_id = $request->input('user_id');
            $log->project_id = $logData['project_id'];
            $log->date = $logData['date'];
            $log->start = $logData['start'];
            $log->end = $logData['end'];
            $log->bs_from = $logData['bs_start'] ?? null;
            $log->bs_to = $logData['bs_end'] ?? null;
            $log->bs_diff = $logData['bs_diff'] ?? null;
            $log->fm_amount = $logData['stueckzahl'] ?? null;
            $log->fm_total = $logData['fm_gesamt'] ?? null;
            $log->fm_day = $logData['fm_day'] ?? null;
            $log->save();
            $lastLog = $log;

            if (!empty($logData['forstwirt_work_entries'])) {
                $this->forstwirtLogService->saveLogs($logData['forstwirt_work_entries'], (int) $log->user_id);
            }
        }

        return redirect()->route($this->route() . '.success', ['worker_id' => (int) $lastLog->user_id]);
    }

    public function getLogOfToday(int $user_id)
    {
        $harvesterLog =$this->logModel()::where('user_id', $user_id)->whereDate('date', today())->first();

        if (!$harvesterLog) {
            $log = ForstwirtLog::where('user_id', $user_id)->whereDate('date', today())->first();
        }

        return $harvesterLog ?? $log;
    }

    public function deleteLogsOfDate(int $user_id, string $date)
    {
        $harvesterLogs = HarvesterLog::where('user_id', $user_id)->whereDate('date', $date)->get();

        foreach ($harvesterLogs as $log) {
            $log->delete();
        }

        $this->forstwirtLogService->deleteLogsFrom($user_id, $date);

    }
}