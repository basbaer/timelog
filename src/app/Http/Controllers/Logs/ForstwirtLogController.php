<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Controller;
use App\Models\ForstwirtWorkingType;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\ForstwirtLog;
use App\Models\ForstwirtLogEntry;

class ForstwirtLogController extends Controller
{
    public function show()
    {
        // Get role of user
        $user = User::findOrFail(Auth::id());
        $isAdmin = $user->isAdmin();
        $name = $user->first_name . ' ' . $user->last_name;
        // Get all open projects for the user's role
        $projects = $user->openProjects()->get();

        // TODO: Check if today is alreay logged
        
        return view('log-forms/log-forstwirt', compact(['projects', 'isAdmin', 'name']));
    }

    public function store(Request $request)
    {
        // If the validation fails, the user will be redirected back to the form with error messages and old input data.
        // The rest of the method will not be executed if validation fails, so we can safely assume that any code after the validation will only run if the input data is valid.
        $mappedLogs = $this->validateForm($request);

        foreach ($mappedLogs as $logData) {
            $log = new ForstwirtLog();
            $log->user_id = Auth::id();
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

        return response()->json(['message' => 'Forstwirt log successfully saved.']);

    }

    public function validateForm(Request $request)
    {
        $workLogs = collect((array) $request->input('work_logs', []))
            ->filter(function (array $workLog) {
                $start = trim((string) ($workLog['start'] ?? ''));
                $end = trim((string) ($workLog['end'] ?? ''));

                $hasHours = collect($workLog['entries'] ?? [])
                    ->pluck('hours')
                    ->contains(fn ($hours) => trim((string) ($hours ?? '')) !== '');

                return !($start === '' && $end === '' && ! $hasHours);
            })
            ->values()
            ->all();

        $request->merge(['work_logs' => $workLogs]);

        $workTypeKeys = ForstwirtWorkingType::all()->pluck('slug')->toArray();

        $validator = Validator::make($request->all(), [
            'log_date' => ['required', 'date'],
            'work_logs' => ['required', 'array', 'min:1'],
            'work_logs.*.project_id' => ['required', 'integer', Rule::exists('projects', 'id')],
            'work_logs.*.start' => ['required', 'date_format:H:i'],
            'work_logs.*.end' => ['required', 'date_format:H:i'],
            'work_logs.*.pause' => ['nullable', 'integer', 'min:0'],
            'work_logs.*.sum' => ['nullable', 'date_format:H:i'],
            'work_logs.*.entries' => ['required', 'array', 'min:1'],
            'work_logs.*.entries.*.type' => ['required', 'string', Rule::in($workTypeKeys)],
            'work_logs.*.entries.*.hours' => ['required', 'numeric', 'min:0'],
            'work_logs.*.entries.*.comment' => ['nullable', 'string', 'max:1000'],
        ]);

        $validator->after(function ($validator) use ($request) {
            foreach ((array) $request->input('work_logs', []) as $projectIndex => $workLog) {
                $types = collect($workLog['entries'] ?? [])
                    ->pluck('type')
                    ->filter()
                    ->values();

                if ($types->count() !== $types->unique()->count()) {
                    $validator->errors()->add(
                        "work_logs.{$projectIndex}.entries",
                        'Each work type can only be selected once per project.'
                    );
                }
            }
        });

        $validated = $validator->validate();

        $logDate = $validated['log_date'];

        $mappedLogs = collect($validated['work_logs'])
            ->map(function (array $workLog) use ($logDate) {
                return [
                    'project_id' => (int) $workLog['project_id'],
                    'date' => $logDate,
                    'start' => $workLog['start'],
                    'end' => $workLog['end'],
                    'pause' => (int) ($workLog['pause'] ?? 0),
                    'sum' => $workLog['sum'] ?? null,
                    'entries' => collect($workLog['entries'])
                        ->map(fn (array $entry) => [
                            'type' => $entry['type'],
                            'hours' => (float) $entry['hours'],
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
}
