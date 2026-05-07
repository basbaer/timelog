<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Logs\BaseLogController;
use App\Models\ForstwirtWorkingType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\ForstwirtLog;
use App\Models\ForstwirtLogEntry;

class ForstwirtLogController extends BaseLogController
{

    // Implement abstract methods from BaseLogController
    public function logModel(): string
    {
        return ForstwirtLog::class;
    }

    public function logEntryModel(): string
    {
        return ForstwirtLogEntry::class;
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

    public function validationRules(): array
    {
        $workTypeKeys = ForstwirtWorkingType::all()->pluck('slug')->toArray();

        return [
            'log_date' => ['required', 'date'],
            'work_logs' => ['required', 'array', 'min:1'],
            'work_logs.*.project_id' => ['required', 'integer', Rule::exists('projects', 'id')],
            'work_logs.*.start' => ['required', 'date_format:H:i'],
            'work_logs.*.end' => ['required', 'date_format:H:i'],
            'work_logs.*.pause' => ['nullable', 'integer', 'min:0'],
            'work_logs.*.sum' => ['nullable', 'date_format:H:i'],
            'work_logs.*.entries' => ['required', 'array', 'min:1'],
            'work_logs.*.entries.*.type' => ['required', 'string', Rule::in($workTypeKeys)],
            'work_logs.*.entries.*.hours' => ['required', 'date_format:H:i'],
            'work_logs.*.entries.*.comment' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function mapValidatedToLogs(array $validated): array
    {
        $logDate = $validated['log_date'];

        return collect($validated['work_logs'])
            ->map(function (array $workLog) use ($logDate) {
                return [
                    'project_id' => (int) $workLog['project_id'],
                    'date' => $logDate,
                    'start' => $workLog['start'],
                    'end' => $workLog['end'],
                    'pause' => (int) ($workLog['pause'] ?? 0),
                    'sum' => $workLog['sum'] ?? null,
                    'entries' => collect($workLog['entries'])
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

        // Example controller mapping result:
        // $mappedLogs = [
        //     [
        //         'project_id' => 123,
        //         'date' => '2026-04-15',
        //         'start' => '07:00',
        //         'end' => '15:30',
        //         'pause' => 30,
        //         'sum' => '07:30',
        //         'entries' => [
        //             ['type' => 'motorsage', 'hours' => 4.0, 'comment' => '...'],
        //         ],
        //     ],
        // ];

        logger()->info('Forstwirt log submission validated', $mappedLogs);

        return $mappedLogs;
    }


    public function store(Request $request)
    {
        $validate = $this->validateForm($request);
        // If the validation fails, the user will be redirected back to the form with error messages and old input data.
        // The rest of the method will not be executed if validation fails, so we can safely assume that any code after the validation will only run if the input data is valid.
        $mappedLogs = $this->mapValidatedToLogs($validate);

        foreach ($mappedLogs as $logData) {
            $log = new ForstwirtLog();
            // Get user_id from the form if the user is an admin, otherwise use the authenticated user's id
            $log->user_id = $request->input('id');
            $log->project_id = $logData['project_id'];
            $log->date = $logData['date'];
            $log->start = $logData['start'];
            $log->end = $logData['end'];
            $log->pause = $logData['pause'] ?? 0;
            $log->sum = $logData['sum'] ?? null;
            $log->save();

            foreach ($logData['entries'] as $entry) {
                $logEntry = new ForstwirtLogEntry();
                $logEntry->forstwirt_log_id = $log->id;
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

    /**
     * Trims whitespace from input data and filters out empty work logs.
     */
    protected function filterRequest(Request $request): array
    {
        
        $workLogs = collect((array) $request->input('work_logs', [])) //if work_logs is null, treat it as an empty array to avoid errors
            ->map(function (array $workLog) {
                $entries = collect($workLog['entries'] ?? [])
                    ->filter(fn(array $entry) => trim((string) ($entry['hours'] ?? '')) !== '')
                    ->values()
                    ->all();

                $workLog['entries'] = $entries;

                return $workLog;
            })
            ->filter(function (array $workLog) {
                // trim removes whitespace, (string) ensures that trim will definetly work
                // ?? '' ensures that if the value is null, it will be treated as an empty string for the trim function
                $start = trim((string) ($workLog['start'] ?? ''));
                $end = trim((string) ($workLog['end'] ?? ''));

                $hasHours = collect($workLog['entries'] ?? [])->isNotEmpty();

                return !($start === '' && $end === '' && ! $hasHours);
            })
            ->values() // reindex the array after filtering
            ->all(); // convert the collection back to a plain array

            return $workLogs;
    }

}
