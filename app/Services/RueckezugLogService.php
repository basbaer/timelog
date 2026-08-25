<?php

namespace App\Services;

use App\Models\RueckezugLog;

class RueckezugLogService extends BaseLogService
{

    public function getModel(): string
    {
        return RueckezugLog::class;
    }

    public function getLogType(): string
    {
        return 'rueckezug';
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
            'loadings' => "Ladungen",
            'average_distance' => "Durchschnittliche Entfernung",
        ];
    }

    public function saveLog(array $logData): RueckezugLog
    {

        $log = new RueckezugLog();
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
        $log->loadings = $logData['loadings'] ?? null;
        $log->average_distance = $logData['average_distance'] ?? null;
        $log->save();

        $log->type = $this->getLogType();

        return $log;
    }

    public function updateLog(RueckezugLog $log, array $logData): RueckezugLog
    {
        $log->project_id = $logData['project_id'];
        $log->date = $logData['date'];
        $log->start = $logData['start'] ?? null;
        $log->end = $logData['end'] ?? null;
        $log->sum = $logData['sum'] ?? null;
        $log->pause = $logData['pause'] ?? 0;
        $log->bs_from = $logData['bs_start'] ?? null;
        $log->bs_to = $logData['bs_end'] ?? null;
        $log->bs_diff = $logData['bs_diff'] ?? null;
        $log->loadings = $logData['loadings'] ?? null;
        $log->average_distance = $logData['average_distance'] ?? null;
        $log->save();

        return $log;
    }

    public function getLastBsTo(int $userId, int $projectId): int
    {
        return (int) RueckezugLog::where('user_id', $userId)
            ->max('bs_to');
    }

    public function getLastAverageDistance(int $userId, int $projectId): int
    {
        $lastLog = RueckezugLog::where('user_id', $userId)
            ->where('project_id', $projectId)
            ->whereNotNull('average_distance')
            ->latest()
            ->first();

        return $lastLog ? $lastLog->average_distance : 0;
    }
}
