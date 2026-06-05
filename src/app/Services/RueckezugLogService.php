<?php

namespace App\Services;

use App\Models\RueckezugLog;
use Illuminate\Support\Collection;

class RueckezugLogService extends BaseLogService
{
    public function __construct()
    {
    }
    protected function getModel(): string
    {
        return RueckezugLog::class;
    }

    protected function getEntryLabel(): ?string
    {
        return 'rueckezug';
    }

    public function saveLogs(array $mappedLogs, int $userId)
    {
        $lastLog = null;

        foreach ($mappedLogs as $logData) {
            $log = new RueckezugLog();
            $log->user_id = $userId;
            $log->project_id = $logData['project_id'];
            $log->date = $logData['date'];
            $log->start = $logData['start'] ?? null;
            $log->end = $logData['end'] ?? null;
            $log->sum = $logData['sum'] ?? null;
            $log->bs_from = $logData['bs_start'] ?? null;
            $log->bs_to = $logData['bs_end'] ?? null;
            $log->bs_diff = $logData['bs_diff'] ?? null;
            $log->loadings = $logData['loadings'] ?? null;
            $log->averarge_distance = $logData['average_distance'] ?? null;
            $log->save();
            $lastLog = $log;

            // forstwirt nested entries are handled by the orchestrator
        }

        return $lastLog;
    }

    public function loadSuccessLogs(int $userId, string $date): Collection
    {
        $rueckezugLogs = RueckezugLog::with(['project'])
            ->where('user_id', $userId)
            ->whereDate('date', $date)
            ->get()
            ->map(function (RueckezugLog $log) {
                $log->entry_label = 'rueckezug';
                return $log;
            });

        return $rueckezugLogs->sortBy(function ($log) {
            return sprintf('%s|%s|%s', $log->project_id, $log->start ?? '', $log->created_at ?? '');
        })->values();
    }


}