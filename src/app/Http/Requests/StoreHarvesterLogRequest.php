<?php

namespace App\Http\Requests;

use App\Models\ForstwirtWorkingType;
use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;

class StoreHarvesterLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $workTypeKeys = ForstwirtWorkingType::all()->pluck('slug')->toArray();

        return [
            'log_date' => ['required', 'date'],
            'work_logs' => ['required', 'array', 'min:1'],
            'work_logs.*' => ['required', 'array'],
            'work_logs.*.start' => ['nullable', 'date_format:H:i', 'required_with:work_logs.*.end'],
            'work_logs.*.end' => ['nullable','date_format:H:i', 'required_with:work_logs.*.start'],
            'work_logs.*.sum' => ['nullable', 'date_format:H:i'],
            'work_logs.*.pause' => ['nullable', 'integer', 'min:0'],
            'work_logs.*.bs_start' => ['nullable', 'numeric', 'min:0', 'required_with:work_logs.*.bs_end'],
            'work_logs.*.bs_end' => ['nullable', 'numeric', 'min:0', 'required_with:work_logs.*.bs_start'],
            'work_logs.*.bs_diff' => ['nullable', 'string'],
            'work_logs.*.stueckzahl' => ['nullable', 'numeric', 'min:0', 'required_with:work_logs.*.fm_gesamt'],
            'work_logs.*.fm_gesamt' => ['nullable', 'numeric', 'min:0', 'required_with:work_logs.*.stueckzahl'],
            'work_logs.*.fm_day' => ['nullable', 'string'],
            'work_logs.*.entries' => ['array'],
            'work_logs.*.entries.*.type' => ['required', 'string', 'in:' . implode(',', $workTypeKeys)],
            'work_logs.*.entries.*.start' => ['required', 'date_format:H:i'],
            'work_logs.*.entries.*.end' => ['required', 'date_format:H:i'],
            'work_logs.*.entries.*.pause' => ['nullable', 'integer', 'min:0'],
            'work_logs.*.entries.*.sum' => ['required', 'date_format:H:i'],
            'work_logs.*.entries.*.comment' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        $messages = [];
        $projects = Project::whereIn('id', array_keys((array) $this->input('work_logs', [])))
            ->get()
            ->keyBy('id');

        foreach ((array) $this->input('work_logs', []) as $projectId => $workLog) {
            $project = $projects->get((int) $projectId);
            $projectLabel = $project
                ? trim($project->location . ' | ' . $project->client, " |")
                : (string) $projectId;

            foreach (['start', 'end'] as $field) {
                if (!array_key_exists($field, $workLog) || trim((string) ($workLog[$field] ?? '')) === '') {
                    $messages["work_logs.$projectId.$field.required"] = __('log_validation.messages.required', [
                        'project' => $projectLabel,
                        'field' => __('log_validation.fields.' . $field),
                    ]);
                }
            }

            if (
                (array_key_exists('bs_start', $workLog) && trim((string) ($workLog['bs_start'] ?? '')) !== '')
                || (array_key_exists('bs_end', $workLog) && trim((string) ($workLog['bs_end'] ?? '')) !== '')
            ) {
                $messages["work_logs.$projectId.bs_end.required_with"] = __('log_validation.messages.required_with', [
                    'project' => $projectLabel,
                    'field' => __('log_validation.fields.bs_end'),
                    'other_field' => __('log_validation.fields.bs_start'),
                ]);
                $messages["work_logs.$projectId.bs_start.required_with"] = __('log_validation.messages.required_with', [
                    'project' => $projectLabel,
                    'field' => __('log_validation.fields.bs_start'),
                    'other_field' => __('log_validation.fields.bs_end'),
                ]);
            }

            if (
                (array_key_exists('stueckzahl', $workLog) && trim((string) ($workLog['stueckzahl'] ?? '')) !== '')
                || (array_key_exists('fm_gesamt', $workLog) && trim((string) ($workLog['fm_gesamt'] ?? '')) !== '')
            ) {
                $messages["work_logs.$projectId.fm_gesamt.required_with"] = __('log_validation.messages.required_with', [
                    'project' => $projectLabel,
                    'field' => __('log_validation.fields.fm_gesamt'),
                    'other_field' => __('log_validation.fields.stueckzahl'),
                ]);
                $messages["work_logs.$projectId.stueckzahl.required_with"] = __('log_validation.messages.required_with', [
                    'project' => $projectLabel,
                    'field' => __('log_validation.fields.stueckzahl'),
                    'other_field' => __('log_validation.fields.fm_gesamt'),
                ]);
            }

            foreach (['stueckzahl', 'fm_gesamt'] as $field) {
                if (array_key_exists($field, $workLog) && trim((string) ($workLog[$field] ?? '')) === '') {
                    $messages["work_logs.$projectId.$field.required"] = __('log_validation.messages.required', [
                        'project' => $projectLabel,
                        'field' => __('log_validation.fields.' . $field),
                    ]);
                }
            }

            foreach (($workLog['entries'] ?? []) as $entryIndex => $entry) {
                if (!is_array($entry)) {
                    continue;
                }

                foreach (['type', 'start', 'end', 'sum'] as $field) {
                    if (!array_key_exists($field, $entry) || trim((string) ($entry[$field] ?? '')) === '') {
                        $messages["work_logs.$projectId.entries.$entryIndex.$field.required"] = __('log_validation.messages.required', [
                            'project' => $projectLabel,
                            'field' => __('log_validation.fields.' . $field),
                        ]);
                    }
                }
            }
        }

        return $messages;
    }

    protected function prepareForValidation(): void
    {
        $projectFields = ['start', 'end', 'sum', 'pause', 'bs_start', 'bs_end', 'bs_diff', 'stueckzahl', 'fm_gesamt', 'fm_day'];
        $decimalFields = ['bs_start', 'bs_end', 'fm_gesamt'];

        $workLogs = collect((array) $this->input('work_logs', []))
            ->map(function (array $workLog) use ($projectFields, $decimalFields) {
                $filteredWorkLog = [];

                foreach ($projectFields as $field) {
                    if (isset($workLog[$field]) && trim((string) $workLog[$field]) !== '') {
                        $filteredWorkLog[$field] = in_array($field, $decimalFields, true)
                            ? str_replace(',', '.', (string) $workLog[$field])
                            : $workLog[$field];
                    }
                }

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

        $this->merge(['work_logs' => $workLogs]);
    }
}