<?php

namespace App\Services;

abstract class BaseLogService
{
    abstract protected function getModel(): string;


    /**
     * Delete all logs for a given user and date.
     */
    public function getLogsFromTo(int $workerId, string $startDate, string $endDate)
    {
       $log_entries = $this->getModel():: 
            where('user_id', $workerId)
            ->whereBetween('date', [$startDate, $endDate])
            ->orderBy('date', 'asc')
            ->orderBy('start', 'asc')
            ->get();

        return $log_entries;

    }

    /**
     * Delete all logs for a given user and date.
     */
    public function deleteLogsFrom(int $user_id, string $date): void
    {
        $logs = $this->getModel()::where('user_id', $user_id)->whereDate('date', $date)->get();

        foreach ($logs as $log) {
            $log->delete();
        }
    }
}