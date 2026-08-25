<?php

namespace App\Services;

use App\Models\ForstwirtLog;
use App\Models\ForstwirtWorkingType;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Override;

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
    public function saveLog(array $logData): ForstwirtLog
    {

        if (isset($logData['id'])) {
            $log = ForstwirtLog::find($logData['id']);
            if (!$log) {
                throw new \Exception("Log with ID {$logData['id']} not found.");
            }
        } else {
            $log = new ForstwirtLog();
        }
        $log->user_id = $logData['worker_id'];
        $log->project_id = $logData['project_id'];
        $log->working_type_id = ForstwirtWorkingType::where('slug', $logData['work_type'])->value('id');
        $log->date = $logData['date'];
        $log->start = $logData['start'];
        $log->end = $logData['end'];
        $log->pause = $logData['pause'] ?? 0;
        $log->sum = $logData['sum'] ?? null;
        $log->comment = $logData['comment'] ?? null;
        $log->save();

        $log->type = $this->getLogType();

        return $log;
    }

    public function getPrefill(ForstwirtLog $log): array
    {

        $prefill = [
            'type' => $log->workingType?->slug ?? '',
            'start' => $log->start ? Carbon::parse($log->start)->format('H:i') : null,
            'end' => $log->end ? Carbon::parse($log->end)->format('H:i') : null,
            'pause' => $log->pause ?? 0,
            'sum' => $log->sum ? Carbon::parse($log->sum)->format('H:i') : null,
            'comment' => $log->comment,
        ];

        return $prefill;
    }

    #[Override]
    public function loadLogs(int $userId, string $date, array $lazyLoad = ['project']): Collection
    {
        return parent::loadLogs($userId, $date, ['project', 'workingType']);
    }
}
