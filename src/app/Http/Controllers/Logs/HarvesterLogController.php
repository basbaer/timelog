<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Logs\BaseLogController;
use App\Models\ForstwirtWorkingType;
use App\Models\HarvesterLog;
use App\Models\HarvesterLogEntry;
use Illuminate\Http\Request;

class HarvesterLogController extends BaseLogController
{
    public function logModel(): string
    {
        return HarvesterLog::class;
    }

    public function logEntryModel(): string
    {
        return HarvesterLogEntry::class;
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
            'work_logs.*.project_id' => ['required', 'integer', 'exists:projects,id'],
            'work_logs.*.start' => ['nullable', 'date_format:H:i'],
            'work_logs.*.end' => ['nullable', 'date_format:H:i'],
            'work_logs.*.bs_start' => ['nullable', 'integer', 'min:0'],
            'work_logs.*.bs_end' => ['nullable', 'integer', 'min:0'],
            'work_logs.*.bs_diff' => ['nullable', 'string'],
            'work_logs.*.stueckzahl' => ['nullable', 'integer', 'min:0'],
            'work_logs.*.fm_gesamt' => ['nullable', 'numeric'],
            'work_logs.*.day_fm' => ['nullable', 'string'],
            'work_logs.*.entries' => ['nullable', 'array'],
            'work_logs.*.entries.*.type' => ['required_with:work_logs.*.entries', 'string', 'in:' . implode(',', $workTypeKeys)],
            'work_logs.*.entries.*.hours' => ['required_with:work_logs.*.entries', 'date_format:H:i'],
            'work_logs.*.entries.*.comment' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function filterRequest(Request $request): array
    {
        $workLogs = collect((array) $request->input('work_logs', []))
            ->map(function (array $workLog) {
                $entries = collect($workLog['entries'] ?? [])
                    ->filter(fn(array $entry) => trim((string) ($entry['hours'] ?? '')) !== '')
                    ->values()
                    ->all();

                $workLog['entries'] = $entries;

                return $workLog;
            })
            ->filter(function (array $workLog) {
                $start = trim((string) ($workLog['start'] ?? ''));
                $end = trim((string) ($workLog['end'] ?? ''));
                $hasHours = collect($workLog['entries'] ?? [])->isNotEmpty();

                $bsStart = trim((string) ($workLog['bs_start'] ?? ''));
                $bsEnd = trim((string) ($workLog['bs_end'] ?? ''));
                $stueckzahl = trim((string) ($workLog['stueckzahl'] ?? ''));
                $fmGesamt = trim((string) ($workLog['fm_gesamt'] ?? ''));

                // keep work log when at least one meaningful field is set
                return !($start === '' && $end === '' && !$hasHours && $bsStart === '' && $bsEnd === '' && $stueckzahl === '' && $fmGesamt === '');
            })
            ->values()
            ->all();

        return $workLogs;
    }

    protected function mapValidatedToLogs(array $validated): array
    {
        $logDate = $validated['log_date'];

        return collect($validated['work_logs'] ?? [])
            ->map(function (array $workLog) use ($logDate) {
                return [
                    'project_id' => (int) $workLog['project_id'],
                    'date' => $logDate,
                    'start' => $workLog['start'] ?? null,
                    'end' => $workLog['end'] ?? null,
                    'bs_start' => isset($workLog['bs_start']) ? (int) $workLog['bs_start'] : null,
                    'bs_end' => isset($workLog['bs_end']) ? (int) $workLog['bs_end'] : null,
                    'bs_diff' => $workLog['bs_diff'] ?? null,
                    'stueckzahl' => isset($workLog['stueckzahl']) ? (int) $workLog['stueckzahl'] : null,
                    'fm_gesamt' => isset($workLog['fm_gesamt']) ? $workLog['fm_gesamt'] : null,
                    'day_fm' => $workLog['day_fm'] ?? null,
                    'entries' => collect($workLog['entries'] ?? [])
                        ->map(fn(array $entry) => [
                            'type' => $entry['type'],
                            'hours' => $entry['hours'],
                            'comment' => $entry['comment'] ?? null,
                        ])
                        ->values()
                        ->all(),
                ];
            })
            ->values()
            ->all();
    }

    protected function store(Request $request)
    {
        $validated = $this->validateForm($request);

        $mappedLogs = $this->mapValidatedToLogs($validated);

        foreach ($mappedLogs as $logData) {
            $log = new HarvesterLog();
            $log->user_id = $request->input('id');
            $log->project_id = $logData['project_id'];
            $log->date = $logData['date'];
            $log->start = $logData['start'] ?? null;
            $log->end = $logData['end'] ?? null;
            $log->bs_from = $logData['bs_start'] ?? null;
            $log->bs_to = $logData['bs_end'] ?? null;
            $log->bs_diff = $logData['bs_diff'] ?? null;
            $log->fm_amount = $logData['stueckzahl'] ?? null;
            $log->fm_total = $logData['fm_gesamt'] ?? null;
            $log->fm_day = $logData['day_fm'] ?? null;
            $log->save();

            foreach ($logData['entries'] as $entry) {
                $logEntry = new HarvesterLogEntry();
                $logEntry->harvester_log_id = $log->id;
                $workingType = ForstwirtWorkingType::where('slug', $entry['type'])->first();
                if ($workingType) {
                    $logEntry->working_type_id = $workingType->id;
                }
                $logEntry->hours = $entry['hours'];
                $logEntry->comment = $entry['comment'] ?? null;
                $logEntry->save();
            }
        }

        return redirect()->route($this->route() . '.success', ['log_id' => $log->id]);
    }


}
