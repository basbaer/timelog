<?php

namespace App\Services;

use App\Models\ForstwirtLog;
use App\Models\ForstwirtWorkingType;

class ForstwirtLogService extends BaseLogService
{
    public function getModel(): string
    {
        return ForstwirtLog::class;
    }

    public function getRelations(): array
    {
        return ['project', 'workingType', 'user.role'];
    }

    public function getLogType(): string
    {
        return 'forstwirt';
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
            'working_type_name' => __('form.working_type'),
            'comment' => __('form.comment'),
        ];
    }

    /**
     * Persist entries in the same shape used by Forstwirt logs.
     */
    public function saveLog(array $logData): ?ForstwirtLog
    {

        $log = new ForstwirtLog();
        $log->id = $logData['id'] ?? null;
        $log->user_id = $logData['user_id'];
        $log->project_id = $logData['project_id'];
        $log->working_type_id = ForstwirtWorkingType::where('slug', $logData['work_type'])->value('id');
        $log->date = $logData['date'];
        $log->start = $logData['start'];
        $log->end = $logData['end'];
        $log->pause = $logData['pause'] ?? 0;
        $log->sum = $logData['sum'] ?? null;
        $log->comment = $logData['comment'] ?? null;
        $log->save();

        $log->log_type = $this->getLogType();

        return $log;
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
                $log->entry_label = 'forstwirt';
                return $log;
            });
    }
}
