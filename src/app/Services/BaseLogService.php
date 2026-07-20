<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

abstract class BaseLogService
{
    abstract protected function getModel(): string;

    protected function getRelations(): array
    {
        return ['project', 'user.role'];
    }

    protected function getEntryLabel(): ?string
    {
        return null;
    }


    /**
     * Delete all logs for a given user and date.
     */
    public function getLogsFromTo(int $workerId, string $startDate, string $endDate, ?int $projectId = null): Collection
    {
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

    public function getLogsForWorker(int $workerId, string $startDate, string $endDate, ?int $projectId = null): Collection
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
                $entry->project_client = $entry->project?->client;
                $entry->project_location = $entry->project?->location;
                $entry->working_type_name = $entry->workingType?->name ?? $entry->user?->role?->slug;

                if ($this->getEntryLabel() !== null) {
                    $entry->entry_label = $this->getEntryLabel();
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

}