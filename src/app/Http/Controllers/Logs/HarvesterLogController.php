<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Logs\BaseLogController;
use App\Models\ForstwirtWorkingType;
use App\Models\HarvesterLog;
use Illuminate\Http\Request;

class HarvesterLogController extends BaseLogController
{
    public function logModel(): string
    {
        return HarvesterLog::class;
    }

    public function workingTypeModel(): string
    {
        return ForstwirtWorkingType::class;
    }

    public function route(): string
    {
        return 'log.harvester';
    }

    public function viewPrefix(): string
    {
        return 'log-harvester';
    }

    public function validationRules(): array
    {
        $workTypeKeys = ForstwirtWorkingType::all()->pluck('slug')->toArray();

        return [
            'log_date' => ['required', 'date'],
            'work_logs' => ['required', 'array', 'min:1'],
            'work_logs.*' => ['required', 'array'],
            'work_logs.*.start' => ['nullable', 'date_format:H:i'],
            'work_logs.*.end' => ['nullable', 'date_format:H:i'],
            //'work_logs.*.pause' => ['nullable', 'integer', 'min:0'],
            //'work_logs.*.sum' => ['required', 'date_format:H:i'],
            'work_logs.*.bs_start' => ['nullable', 'integer', 'min:0'],
            'work_logs.*.bs_end' => ['nullable', 'integer', 'min:0'],
            'work_logs.*.bs_diff' => ['nullable', 'string'],
            'work_logs.*.stueckzahl' => ['nullable', 'integer', 'min:0'],
            'work_logs.*.fm_gesamt' => ['nullable', 'numeric'],
            'work_logs.*.day_fm' => ['nullable', 'string'],
            // Entries moved into `work_logs.<projectId>.entries` by filterRequest()
            'work_logs.*.entries' => ['array'],
            'work_logs.*.entries.*.type' => ['required', 'string', 'in:' . implode(',', $workTypeKeys)],
            'work_logs.*.entries.*.start' => ['required', 'date_format:H:i'],
            'work_logs.*.entries.*.end' => ['required', 'date_format:H:i'],
            'work_logs.*.entries.*.pause' => ['nullable', 'integer', 'min:0'],
            'work_logs.*.entries.*.sum' => ['required', 'date_format:H:i'],
            'work_logs.*.entries.*.comment' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function filterRequest(Request $request): array
    {
        //$projectFields = ['start', 'end','pause', 'sum', 'bs_start', 'bs_end', 'bs_diff', 'stueckzahl', 'fm_gesamt'];
        $projectFields = ['start', 'end', 'bs_start', 'bs_end', 'bs_diff', 'stueckzahl', 'fm_gesamt', 'day_fm'];

        return collect((array) $request->input('work_logs', []))
            ->map(function (array $workLog) use ($projectFields) {
                // Holding the filtered log data
                $filteredWorkLog = [];

                // Check project-level fields and include them if they have input
                foreach ($projectFields as $field) {
                    // Check if the field is set and not empty after trimming whitespace
                    if (isset($workLog[$field]) && trim((string) $workLog[$field]) !== '') {
                        $filteredWorkLog[$field] = $workLog[$field];
                    }
                }

                // Check work entry fields and include entries that have at least one field with input
                // Note: They need to be included in order to show the corresponding validation errors if the required fields for the work entry are not filled out, even if the project-level fields are empty
                // collect numeric child entries and move them to `entries`
                $entries = [];
                foreach ($workLog as $key => $entry) {
                    if (!is_numeric($key) || !is_array($entry)) {
                        continue;
                    }

                    $hasEntryInput = collect([
                        $entry['start'] ?? '',
                        $entry['end'] ?? '',
                        $entry['comment'] ?? '',
                    ])->contains(fn($value) => trim((string) $value) !== '');

                    if ($hasEntryInput) {
                        $entries[] = $entry;
                    }
                }

                if (!empty($entries)) {
                    $filteredWorkLog['entries'] = array_values($entries);
                }

                return $filteredWorkLog;
            })
            ->filter(function (array $workLog) use ($projectFields) {
                $hasProjectField = collect($projectFields)
                    ->contains(fn(string $field) => trim((string) ($workLog[$field] ?? '')) !== '');

                $hasEntries = !empty($workLog['entries'] ?? []);

                return $hasProjectField || $hasEntries;
            })
            ->all();
    }

    protected function mapValidatedToLogs(array $validated): array
    {
        $logDate = $validated['log_date'];

        return collect($validated['work_logs'] ?? [])
            ->map(function (array $workLog, $projectId) use ($logDate) {
                return [
                    'project_id' => (int) $projectId,
                    'date' => $logDate,
                    'start' => $workLog['start'] ?? null,
                    'end' => $workLog['end'] ?? null,
                    'bs_start' => isset($workLog['bs_start']) ? (int) $workLog['bs_start'] : null,
                    'bs_end' => isset($workLog['bs_end']) ? (int) $workLog['bs_end'] : null,
                    'bs_diff' => $workLog['bs_diff'] ?? null,
                    'stueckzahl' => isset($workLog['stueckzahl']) ? (int) $workLog['stueckzahl'] : null,
                    'fm_gesamt' => isset($workLog['fm_gesamt']) ? $workLog['fm_gesamt'] : null,
                    'day_fm' => $workLog['day_fm'] ?? null,
                    'forstwirt_work_entries' => collect($workLog['entries'] ?? [])
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
                        ->values()
                        ->all(),
                ];
            })
            ->values()
            ->all();
    }

    public function store(Request $request)
    {
        $validated = $this->validateForm($request);
        $mappedLogs = $this->mapValidatedToLogs($validated);
        $lastLog = null;

        foreach ($mappedLogs as $logData) {
            $log = new HarvesterLog();
            $log->user_id = $request->input('user_id');
            $log->project_id = $logData['project_id'];
            $log->date = $logData['date'];
            $log->start = $logData['start'];
            $log->end = $logData['end'];
            $log->bs_from = $logData['bs_start'] ?? null;
            $log->bs_to = $logData['bs_end'] ?? null;
            $log->bs_diff = $logData['bs_diff'] ?? null;
            $log->fm_amount = $logData['stueckzahl'] ?? null;
            $log->fm_total = $logData['fm_gesamt'] ?? null;
            $log->fm_day = $logData['day_fm'] ?? null;
            $log->save();
            $lastLog = $log;

            if (!empty($logData['forstwirt_work_entries'])) {
                $this->saveForstwirtLogs($logData['forstwirt_work_entries'], (int) $log->user_id);
            }
        }

        return redirect()->route($this->route() . '.success', ['log_id' => $lastLog->id]);
    }
}