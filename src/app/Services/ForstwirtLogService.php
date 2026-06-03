<?php

namespace App\Services;

use App\Models\ForstwirtLog;
use App\Models\ForstwirtWorkingType;

class ForstwirtLogService
{

    public function deleteLogsFrom(int $user_id, string $date): void
    {
        $logs = ForstwirtLog::where('user_id', $user_id)->whereDate('date', $date)->get();

        foreach ($logs as $log) {
            $log->delete();
        }
    }


    /**
     * Persist entries in the same shape used by Forstwirt logs.
     */
    public function saveLogs(array $mappedLogs, int $userId): ?ForstwirtLog
    {
        $lastLog = null;

        foreach ($mappedLogs as $logData) {
            $log = new ForstwirtLog();
            $log->user_id = $userId;
            $log->project_id = $logData['project_id'];
            $log->working_type_id = ForstwirtWorkingType::where('slug', $logData['type'])->value('id');
            $log->date = $logData['date'];
            $log->start = $logData['start'];
            $log->end = $logData['end'];
            $log->pause = $logData['pause'] ?? 0;
            $log->sum = $logData['sum'] ?? null;
            $log->comment = $logData['comment'] ?? null;
            $log->save();

            $lastLog = $log;
        }

        return $lastLog;
    }
}