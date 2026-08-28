<?php

namespace App\Services;

use App\Models\HarvesterLog;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

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
        if (isset($logData['id'])) {
            $log = HarvesterLog::find($logData['id']);
            if (!$log) {
                throw new \Exception("Log with ID {$logData['id']} not found.");
            }
        } else {
            $log = new HarvesterLog();
        }
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
        $log->fm_amount = $logData['fm_amount'] ?? null;
        $log->fm_total = $logData['fm_total'] ?? null;
        $log->fm_day = $logData['fm_day'] ?? null;
        $log->save();

        $log->type = $this->getLogType();
        $log->pieces_day = $logData['pieces_day'] ?? null;

        return $log;
    }


    public function getPrefill(HarvesterLog $log): array
    {
        $prefill = [
            'project_id' => $log->project_id,
            'start' => $log->start ? Carbon::parse($log->start)->format('H:i') : null,
            'end' => $log->end ? Carbon::parse($log->end)->format('H:i') : null,
            'pause' => $log->pause ?? 0,
            'sum' => $log->sum ? Carbon::parse($log->sum)->format('H:i') : null,
            'bs_start' => $log->bs_from,
            'bs_end' => $log->bs_to,
            'bs_diff' => $log->bs_diff,
            'fm_amount' => $log->fm_amount,
            'fm_total' => $log->fm_total,
            'fm_day' => $log->fm_day,
            'type' => $this->getLogType(),
            'pieces_day' => $log->pieces_day,
        ];

        return $prefill;
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

    public function getSecondLastFmAmount(int $userId, int $projectId)
    {
        $lastLog = HarvesterLog::where('user_id', $userId)
            ->where('project_id', $projectId)
            ->whereNotNull('fm_amount')
            ->orderByDesc('date')
            ->orderByDesc('created_at')
            ->skip(1)
            ->first();

        return $lastLog ? $lastLog->fm_amount : 0;
    }

    public function getLastBsTo(int $userId, int $projectId): float
    {
        return (int) HarvesterLog::where('user_id', $userId)
            ->max('bs_to');
    }

    public function load(int $userId, string $date): Collection
    {
        return parent::loadLogs($userId, $date, ['project'])
            ->map(function ($log) {
                $log->type = $this->getLogType();
                $log->projectTitle = $log->project->title;
                $log->pieces_day = $log->fm_amount - $this->getSecondLastFmAmount($log->user_id, $log->project_id);
                return $log;
            });
    }

    public function getLogById(int $userId, int $logId): Model
    {
        $log =  parent::getLogById($userId, $logId);
        $log->pieces_day = $log->fm_amount - $this->getSecondLastFmAmount($log->user_id, $log->project_id);
        return $log;
    }
}
