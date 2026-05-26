<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Logs\BaseLogController;
use App\Models\ForstwirtLog;
use App\Models\ForstwirtWorkingType;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

    public function validationRules(): array
    {
        $workTypeKeys = ForstwirtWorkingType::all()->pluck('slug')->toArray();

        return [
            'log_date' => ['required', 'date'],
            'work_logs' => ['required', 'array', 'min:1'],
            'work_logs.*' => ['required', 'array', 'min:1'],
            'work_logs.*.*.type' => ['required', 'string', Rule::in($workTypeKeys)],
            'work_logs.*.*.start' => ['required', 'date_format:H:i'],
            'work_logs.*.*.end' => ['required', 'date_format:H:i'],
            'work_logs.*.*.pause' => ['nullable', 'integer', 'min:0'],
            'work_logs.*.*.sum' => ['required', 'date_format:H:i'],
            'work_logs.*.*.comment' => ['nullable', 'string', 'max:1000'],
        ];
    }

    protected function filterRequest(Request $request): array
    {
        // Filtere die Eingabe, um nur die tatsächlich ausgefüllten Log-Einträge zu behalten
        // Note: Nutzung der collect()-Funktion von Laravel, um die Daten zu transformieren und zu filtern
        // Collection ist eine Wrapper class zur einfacheren Handhabung von Arrays
        // collect() erstellt eine Collection aus einem Array
        return collect((array) $request->input('work_logs', []))
            //map() wendet die Funktion auf jedes Element der Collection an und gibt eine neue Collection zurück
            ->map(function (array $projectWorkLogs) {
                return collect($projectWorkLogs)
                    ->filter(fn($entry) => is_array($entry)) //php Arrow-Function: Kürzere Syntax für anonyme Funktionen. Hier wird geprüft, ob $entry ein Array ist, um ungültige Einträge zu entfernen.
                    ->filter(fn(array $entry) => 
                    // Ein Eintrag wird behalten, wenn mindestens eines der Felder 'start', 'end' oder 'comment' ausgefüllt ist (nicht nur Leerzeichen)
                        trim((string) ($entry['start'] ?? '')) !== ''
                        || trim((string) ($entry['end'] ?? '')) !== ''
                        || trim((string) ($entry['comment'] ?? '')) !== '')
                    ->values()
                    ->all();
            })
            ->filter(fn(array $projectWorkLogs) => !empty($projectWorkLogs))
            ->all();
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

    public function store(Request $request)
    {
        $validated = $this->validateForm($request);
        $mappedLogs = $this->mapValidatedToLogs($validated);

        $lastLog = $this->saveForstwirtLogs($mappedLogs, $request->input('user_id'));

        return redirect()->route($this->route() . '.success', ['log_id' => $lastLog->id]);
    }
}