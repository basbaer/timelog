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

    private function getServiceForSlug(string $slug): BaseLogService
    {
        return match ($slug) {
            Role::FORSTWIRT => $this->forstwirtLogService,
            Role::HARVESTER => $this->harvesterLogService,
            Role::RUECKEZUG => $this->rueckezugLogService,
            default => throw new \InvalidArgumentException("Unbekannte Rolle: {$slug}"),
        };
    }

    public function getPrintTableHeadersFor(string $role): array
    {
        $service = $this->getServiceForSlug($role);

        return $service->getPrintTableHeaders();
    }

    public function deleteLogsFrom(User|int $worker, string $date): void
    {
        if (is_int($worker)) {
            $worker = User::findOrFail($worker);
        }

        $this->getServiceFor($worker)->each(function ($service) use ($worker, $date) {
            $service->deleteLogsFrom($worker->id, $date);
        });
    }

    public function saveLog(array $logData)
    {
        $log_type = $logData['log_type'] ?? null;

        $service = $this->getServiceForSlug($log_type);

        return $service->saveLog($logData);
    }

    public function loadLogs(User|int $worker, string $date): Collection
    {
        if (is_int($worker)) {
            $worker = User::findOrFail($worker);
        }

        $logs = $this->getServiceFor($worker)
            ->flatMap(fn($service) => $service->load($worker->id, $date))
            ->sortBy(fn($log) => sprintf('%s|%s', $log->start ?? '', $log->created_at ?? ''))
            ->values();

        return $logs;
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
            $service = $this->getServiceForSlug($slug);

            $service->deleteLog($logId);
        }
    }
}
