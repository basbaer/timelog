<?php

namespace App\Services;

use App\Models\HarvesterLog;

class HarvesterLogService extends BaseLogService
{

    public function getModel(): string
    {
        return HarvesterLog::class;
    }

    public function getLogType(): string
    {
        return 'harvester';
    }

    public function getPrintTableHeaders(): array
    {
        return [
            'date' => __('form.date'),
            'start' => __('form.from'),
            'end' => __('form.to'),
            'pause' => __('form.pause'),
            'sum' => __('form.working_time'),
            'title' => __('form.project'),
            'bs_from' => "BS von",
            'bs_to' => "BS bis",
            'bs_diff' => "BS Differenz",
            'fm_amount' => "FM Menge",
            'fm_total' => "FM Gesamt",
            'fm_day' => "FM Tag",
        ];
    }

    public function saveLog(array $logData): HarvesterLog
    {
        $log = new HarvesterLog();
        $log->id = $logData['id'] ?? null;
        $log->user_id = $logData['worker_id'];
        $log->project_id = $logData['project_id'];
        $log->date = $logData['date'];
        $log->start = $logData['start'] ?? null;
        $log->end = $logData['end'] ?? null;
        $log->sum = $logData['sum'] ?? null;
        $log->pause = $logData['pause'] ?? 0;
        $log->bs_from = $logData['bs_start'] ?? null;
        $log->bs_to = $logData['bs_end'] ?? null;
        $log->bs_diff = $logData['bs_diff'] ?? null;
        $log->fm_amount = $logData['stueckzahl'] ?? null;
        $log->fm_total = $logData['fm_gesamt'] ?? null;
        $log->fm_day = $logData['fm_day'] ?? null;
        $log->save();

        $log->type = $this->getLogType();

        return $log;
    }


    public function getLastFmAmount(int $userId, int $projectId): float
    {
        $lastLog = HarvesterLog::where('user_id', $userId)
            ->where('project_id', $projectId)
            ->whereNotNull('fm_amount')
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->first();

        return $lastLog ? $lastLog->fm_amount : 0;
    }

    public function getLastFmTotal(int $userId, int $projectId): float
    {
        $lastLog = HarvesterLog::where('user_id', $userId)
            ->where('project_id', $projectId)
            ->whereNotNull('fm_total')
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->first();

        return $lastLog ? $lastLog->fm_total : 0;
    }

    public function getLastBsTo(int $userId, int $projectId): float
    {
        return (int) HarvesterLog::where('user_id', $userId)
            ->max('bs_to');
    }
}
