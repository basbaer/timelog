<?php

namespace App\Http\Controllers\Logs;

use App\Http\Requests\StoreHarvesterLogRequest;
use App\Models\ForstwirtWorkingType;
use App\Models\HarvesterLog;
use Illuminate\Http\RedirectResponse;

class HarvesterLogController extends BaseLogController
{
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

    public function viewPrefix(): string
    {
        return 'log-harvester';
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
                    'day_fm' => $workLog['day_fm'] ?? null,
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
            $log->fm_day = $logData['day_fm'] ?? null;
            $log->save();
            $lastLog = $log;

            if (!empty($logData['forstwirt_work_entries'])) {
                $this->saveForstwirtLogs($logData['forstwirt_work_entries'], (int) $log->user_id);
            }
        }

        return redirect()->route($this->route() . '.success', ['log_id' => $lastLog->id]);
    }
}