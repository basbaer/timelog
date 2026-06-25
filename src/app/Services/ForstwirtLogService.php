<?php

namespace App\Services;

use App\Models\ForstwirtLog;
use App\Models\ForstwirtWorkingType;

class ForstwirtLogService extends BaseLogService
{
    protected function getModel(): string
    {
        return ForstwirtLog::class;
    }

    protected function getRelations(): array
    {
        return ['project', 'workingType', 'user.role'];
    }

    protected function getEntryLabel(): ?string
    {
        return 'forstwirt';
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

    public function loadSuccessLogs(int $userId, string $date)
    {
        return $this->getModel()::with(['project', 'workingType'])
            ->where('user_id', $userId)
            ->whereDate('date', $date)
            ->orderBy('project_id')
            ->orderBy('start')
            ->get()
            ->map(function ($log) {
                $log->entry_label ='forstwirt';
                return $log;
            });
    }

}