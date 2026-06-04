<?php

namespace App\Http\Requests;

use App\Models\ForstwirtWorkingType;
use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;

class StoreRueckezugLogRequest extends FormRequest
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
            'work_logs.*' => ['array'],
            'work_logs.*.start' => ['date_format:H:i', 'required_with:work_logs.*.end'],
            'work_logs.*.end' => ['date_format:H:i', 'required_with:work_logs.*.start'],
            'work_logs.*.sum' => ['nullable', 'date_format:H:i'],
            'work_logs.*.bs_from' => ['nullable', 'numeric', 'required_with:work_logs.*.bs_to'],
            'work_logs.*.bs_to' => ['nullable', 'numeric', 'required_with:work_logs.*.bs_from'],
            'work_logs.*.bs_diff' => ['nullable', 'numeric'],
            'work_logs.*.loadings' => ['nullable', 'numeric'],
            'work_logs.*.average_distance' => ['nullable', 'numeric'],
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
                (array_key_exists('start', $workLog) && trim((string) ($workLog['start'] ?? '')) !== '')
                || (array_key_exists('end', $workLog) && trim((string) ($workLog['end'] ?? '')) !== '')
            ) {
                $messages["work_logs.$projectId.start.required_with"] = __('log_validation.messages.required_with', [
                    'project' => $projectLabel,
                    'field' => __('log_validation.fields.start'),
                    'other_field' => __('log_validation.fields.end'),
                ]);
                $messages["work_logs.$projectId.end.required_with"] = __('log_validation.messages.required_with', [
                    'project' => $projectLabel,
                    'field' => __('log_validation.fields.end'),
                    'other_field' => __('log_validation.fields.start'),
                ]);
            }

            if (
                (array_key_exists('bs_from', $workLog) && trim((string) ($workLog['bs_from'] ?? '')) !== '')
                || (array_key_exists('bs_to', $workLog) && trim((string) ($workLog['bs_to'] ?? '')) !== '')
            ) {
                $messages["work_logs.$projectId.bs_to.required_with"] = __('log_validation.messages.required_with', [
                    'project' => $projectLabel,
                    'field' => __('log_validation.fields.bs_to'),
                    'other_field' => __('log_validation.fields.bs_from'),
                ]);
                $messages["work_logs.$projectId.bs_from.required_with"] = __('log_validation.messages.required_with', [
                    'project' => $projectLabel,
                    'field' => __('log_validation.fields.bs_from'),
                    'other_field' => __('log_validation.fields.bs_to'),
                ]);
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
        $projectFields = ['start', 'end', 'sum', 'bs_from', 'bs_to', 'bs_diff', 'loadings', 'average_distance'];

        $workLogs = collect((array) $this->input('work_logs', []))
            ->map(function (array $workLog) use ($projectFields) {
                $filteredWorkLog = [];

                foreach ($projectFields as $field) {
                    if (isset($workLog[$field]) && trim((string) $workLog[$field]) !== '') {
                        $filteredWorkLog[$field] = $workLog[$field];
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