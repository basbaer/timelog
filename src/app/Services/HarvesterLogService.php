<?php

namespace App\Services;

use App\Models\HarvesterLog;
use Illuminate\Support\Collection;

class HarvesterLogService extends BaseLogService
{
    protected function getModel(): string
    {
        return HarvesterLog::class;
    }

    protected function getEntryLabel(): ?string
    {
        return 'harvester';
    }
    public function saveLogs(array $mappedLogs, int $userId)
    {
        $lastLog = null;

        foreach ($mappedLogs as $logData) {
            $log = new HarvesterLog();
            $log->user_id = $userId;
            $log->project_id = $logData['project_id'];
            $log->date = $logData['date'];
            $log->start = $logData['start'] ?? null;
            $log->end = $logData['end'] ?? null;
            $log->sum = $logData['sum'] ?? null;
            $log->bs_from = $logData['bs_start'] ?? null;
            $log->bs_to = $logData['bs_end'] ?? null;
            $log->bs_diff = $logData['bs_diff'] ?? null;
            $log->fm_amount = $logData['stueckzahl'] ?? null;
            $log->fm_total = $logData['fm_gesamt'] ?? null;
            $log->fm_day = $logData['fm_day'] ?? null;
            $log->save();
            $lastLog = $log;
        }

        return $lastLog;
    }

    public function loadSuccessLogs(int $userId, string $date): Collection
    {
        $harvesterLogs = HarvesterLog::with(['project'])
            ->where('user_id', $userId)
            ->whereDate('date', $date)
            ->get()
            ->map(function (HarvesterLog $log) {
                $log->entry_label = 'harvester';
                return $log;
            });
        return $harvesterLogs->sortBy(function ($log) {
            return sprintf('%s|%s|%s', $log->project_id, $log->start ?? '', $log->created_at ?? '');
        })->values();
    }

}