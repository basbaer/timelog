<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Http\JsonResponse;
use App\Models\ForstwirtLog;
use App\Models\HarvesterLog;
use App\Models\RueckezugLog;

abstract class BaseLogService
{
    abstract public function getPrintTableHeaders(): array;
    abstract public function getModel(): string;
    abstract public function getLogType(): string;
    abstract public function saveLog(array $logData): ForstwirtLog|HarvesterLog|RueckezugLog|null;

    protected function getRelations(): array
    {
        return ['project', 'user.role'];
    }

    public function getJsonResponseLogSummery(Model $log): JsonResponse
    {
        $json = response()->json([
            'success' => true,
            'html' => view('log-forms.partials.log-summary-item', ['savedLog' => $log])->render(),
        ]);
        return $json;
    }


    /**
     * Delete all logs for a given user and date.
     */
    public function getLogsFromTo(int $workerId, ?string $startDate, ?string $endDate, ?int $projectId = null): Collection
    {
        if ($startDate === null || $endDate === null) {
            return $this->getModel()::with($this->getRelations())
            ->where('user_id', $workerId)
            ->when($projectId !== null, function ($query) use ($projectId) {
                $query->where('project_id', $projectId);
            })
            ->orderBy('date', 'asc')
            ->orderBy('start', 'asc')
            ->get();
        }

       return $this->getModel()::with($this->getRelations())
            ->where('user_id', $workerId)
            ->whereBetween('date', [$startDate, $endDate])
            ->when($projectId !== null, function ($query) use ($projectId) {
                $query->where('project_id', $projectId);
            })
            ->orderBy('date', 'asc')
            ->orderBy('start', 'asc')
            ->get();
    }

    public function getLogsForWorker(int $workerId, ?string $startDate, ?string $endDate, ?int $projectId = null): Collection
    {
        $lastDate = null;

        return $this->getLogsFromTo($workerId, $startDate, $endDate, $projectId)
            ->sortBy(function (Model $entry) {
                return sprintf('%s|%s|%s', $entry->date ?? '', $entry->start ?? '', $entry->created_at ?? '');
            })
            ->values()
            ->map(function (Model $entry) use (&$lastDate) {
                $originalDate = Carbon::parse($entry->date);
                $entry->date_raw = $originalDate->toDateString();
                $entry->start_raw = $entry->start;

                $entry->weekday = __('admin.' . $originalDate->format('l'));
                $entry->date = $originalDate->format('d.m.y');
                $entry->start = $this->formatTime($entry->start);
                $entry->end = $this->formatTime($entry->end);
                $entry->pause = $this->formatPause($entry->pause ?? null);
                $entry->sum = $this->formatTime($entry->sum ?? null);
                $entry->title = $entry->project?->location . " | " . $entry->project?->client;
                $entry->project_client = $entry->project?->client;
                $entry->project_location = $entry->project?->location;
                $entry->working_type_name = $entry->workingType?->name ?? $entry->user?->role?->slug;

                // Add entry_label if getEntryLabel() returns a non-null value
                // This allows subclasses to specify a label for the log entries they handle.
                if ($this->getLogType() !== null) {
                    $entry->entry_label = $this->getLogType();
                }

                if ($lastDate !== $entry->date_raw) {
                    $entry->show_date = true;
                    $lastDate = $entry->date_raw;
                } else {
                    $entry->show_date = false;
                }

                return $entry;
            });
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

    /**
     * Delete a specific log entry by its ID.
     */
    public function deleteLog(int $log_id): void
    {
        $log = $this->getModel()::findOrFail($log_id);
        $log->delete();
    }

    protected function formatTime(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        return Carbon::parse($value)->format('H:i');
    }

    protected function formatPause(mixed $value): ?string
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return Carbon::createFromTime(0, 0)->addMinutes((int) $value)->format('H:i');
        }

        return $this->formatTime($value);
    }

    public function getLogOfToday(int $user_id)
    {
        return $this->getModel()::where('user_id', $user_id)->whereDate('date', today())->first();
    }

    public function loadLogs(int $userId, string $date): Collection
    {
        return  $this->getModel()::with(['project'])
            ->where('user_id', $userId)
            ->whereDate('date', $date)
            ->orderBy('start')
            ->get()
            ->map(function($log){
                $log->type = $this->getLogType();
                $log->projectTitle = $log->project->title;
                return $log;
            });

    }

}