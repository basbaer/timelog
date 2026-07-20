<?php

namespace App\Services;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Collection;
use App\Services\ForstwirtLogService;

class WorkerLogService
{
    public function __construct(
        private readonly ForstwirtLogService $forstwirtLogService,
        private readonly HarvesterLogService $harvesterLogService,
        private readonly RueckezugLogService $rueckezugLogService
    ) {}

  public function deleteLogsFrom(User|int $worker, string $date): void
{
    if (is_int($worker)) {
        $worker = User::findOrFail($worker);
    }

    $this->getServiceFor($worker)->each(function ($service) use ($worker, $date) {
        $service->deleteLogsFrom($worker->id, $date);
    });
}

    public function saveLogs(User|int $worker, array $mappedLogs)
    {
        if (is_int($worker)) {
            $worker = User::findOrFail($worker);
        }

        $services = $this->getServiceFor($worker);
        $primary = $services->first();
        $forstwirt = $services->first(fn($s) => $s instanceof ForstwirtLogService) ?? null;

        $lastLog = null;

        foreach ($mappedLogs as $logData) {
            $lastLog = $primary->saveLogs([$logData], $worker->id);

            if (!empty($logData['forstwirt_work_entries']) && $forstwirt) {
                $forstwirt->saveLogs($logData['forstwirt_work_entries'], (int) $worker->id);
            }
        }

        return $lastLog;
    }

    public function loadSuccessLogs(User|int $worker, string $date): Collection
    {
        if (is_int($worker)) {
            $worker = User::findOrFail($worker);
        }

        return $this->getServiceFor($worker)
            ->flatMap(fn($service) => $service->loadSuccessLogs($worker->id, $date))
            ->sortBy(fn($log) => sprintf('%s|%s|%s', $log->project_id, $log->start ?? '', $log->created_at ?? ''))
            ->values();
    }

    public function getLogOfToday(User|int $worker)
    {
        if (is_int($worker)) {
            $worker = User::findOrFail($worker);
        }

        foreach ($this->getServiceFor($worker) as $service) {
            $log = $service->getLogOfToday($worker->id);
            if ($log) {
                return $log;
            }
        }

        return null;
    }

    public function getLogsFor(User|int $worker, ?string $startDate = null, ?string $endDate = null, ?int $projectId = null): Collection
    {
        if (is_int($worker)) {
            $worker = User::findOrFail($worker);
        }

        $logEntries = $this->getServiceFor($worker)
            ->flatMap(function (BaseLogService $service) use ($worker, $startDate, $endDate, $projectId) {
                return $service->getLogsForWorker($worker->id, $startDate, $endDate, $projectId);
            })
            ->sortBy(function ($log) {
                return sprintf('%s|%s|%s', $log->date_raw ?? '', $log->start_raw ?? '', $log->created_at ?? '');
            })
            ->values();

        $lastDate = null;

        return $logEntries->map(function ($log) use (&$lastDate) {
            if ($lastDate !== $log->date_raw) {
                $log->show_date = true;
                $lastDate = $log->date_raw;
            } else {
                $log->show_date = false;
            }

            return $log;
        });
    }

    public function getLogsForProject(User|int $worker, int $projectId): Collection
    {
        if (is_int($worker)) {
            $worker = User::findOrFail($worker);
        }

        return $this->getServiceFor($worker)
            ->flatMap(fn($service) => $service->getLogsForWorker($worker->id, $startDate = null, $endDate = null, $projectId = $projectId))
            ->sortBy(fn($log) => sprintf('%s|%s|%s', $log->date_raw ?? '', $log->start_raw ?? '', $log->created_at ?? ''))
            ->values();
    }

    private function getServiceFor(User|int $worker): Collection
    {
        if (is_int($worker)) {
            $worker = User::findOrFail($worker);
        }

        return match ($worker->role?->slug) {
            Role::FORSTWIRT => collect([$this->forstwirtLogService]),
            Role::HARVESTER => collect([$this->harvesterLogService, $this->forstwirtLogService]),
            Role::RUECKEZUG => collect([$this->rueckezugLogService, $this->forstwirtLogService]),
            default => throw new \InvalidArgumentException("Unbekannte Rolle: {$worker->role?->slug}"),
        };
    }

    public function deleteLog(int $logId, ?string $slug = null): void
    {
        if ($slug) {
            $service = match ($slug) {
                Role::FORSTWIRT => $this->forstwirtLogService,
                Role::HARVESTER => $this->harvesterLogService,
                Role::RUECKEZUG => $this->rueckezugLogService,
                default => throw new \InvalidArgumentException("Unbekannte Rolle: {$slug}"),
            };

            $service->deleteLog($logId);
        }
    }
}