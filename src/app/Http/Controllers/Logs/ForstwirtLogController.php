<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Logs\BaseLogController;
use App\Http\Requests\StoreForstwirtLogRequest;
use App\Models\ForstwirtLog;
use App\Models\ForstwirtWorkingType;
use Illuminate\Http\RedirectResponse;

class ForstwirtLogController extends BaseLogController
{
    public function logModel(): string
    {
        return ForstwirtLog::class;
    }

    public function workingTypeModel(): string
    {
        return ForstwirtWorkingType::class;
    }

    public function route(): string
    {
        return 'log.forstwirt';
    }

    public function viewPrefix(): string
    {
        return 'log-forstwirt';
    }
    protected function mapValidatedToLogs(array $validated): array
    {
        $logDate = $validated['log_date'];

        return collect($validated['work_logs'] ?? [])
            ->flatMap(function (array $projectWorkLogs, $projectId) use ($logDate) {
                return collect($projectWorkLogs)
                    ->filter(fn(array $entry) => trim((string) ($entry['type'] ?? '')) !== '')
                    ->map(fn(array $entry) => [
                        'project_id' => (int) $projectId,
                        'date' => $logDate,
                        'type' => $entry['type'],
                        'start' => $entry['start'],
                        'end' => $entry['end'],
                        'pause' => isset($entry['pause']) ? (int) $entry['pause'] : 0,
                        'sum' => $entry['sum'] ?? null,
                        'comment' => $entry['comment'] ?? null,
                    ])
                    ->values();
            })
            ->values()
            ->all();
    }

    public function store(StoreForstwirtLogRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $mappedLogs = $this->mapValidatedToLogs($validated);

        $lastLog = $this->saveForstwirtLogs($mappedLogs, $request->input('user_id'));

        return redirect()->route($this->route() . '.success', ['log_id' => $lastLog->id]);
    }
}