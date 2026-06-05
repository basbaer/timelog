<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Collection;

class WorkerLogService
{
    public function __construct(
        private readonly ForstwirtLogService $forstwirtLogService,
        private readonly HarvesterLogService $harvesterLogService,
        private readonly RueckezugLogService $rueckezugLogService
    ) {}

    public function deleteLogsFrom(User $worker): void
    {
        $this->getServiceFor($worker)->each(function ($service) use ($worker) {
            $service->deleteLogsFrom($worker->id);
        });
    }

    public function getLogsFor(User $worker): Collection
    {
        return $this->getServiceFor($worker)->flatMap(function ($service) use ($worker) {
            return $service->getLogsForWorker($worker->id);
        });
    }

    private function getServiceFor(User $worker): Collection
    {
        return match($worker->role) {
            'forstwirt' => collect([$this->forstwirtLogService]),
            'harvester' => collect([$this->harvesterLogService, $this->forstwirtLogService]),
            'rueckezug' => collect([$this->rueckezugLogService, $this->forstwirtLogService]),
            default => throw new \InvalidArgumentException("Unbekannte Rolle: {$worker->role}"),
        };
    }
}