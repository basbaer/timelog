<?php

namespace App\Http\Requests;

use App\Models\ForstwirtWorkingType;
use App\Models\Project;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreForstwirtLogRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // Authorization is handled in the controller's middleware, so we can allow all requests here.
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
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
            'work_logs.*.*.sum' => ['date_format:H:i'],
            'work_logs.*.*.comment' => ['nullable', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        $messages = [];
        $projects = Project::whereIn('id', array_keys((array) $this->input('work_logs', [])))
            ->get()
            ->keyBy('id');

        foreach ((array) $this->input('work_logs', []) as $projectId => $projectWorkLogs) {
            $project = $projects->get((int) $projectId);
            $projectLabel = $project
                ? trim($project->location . ' | ' . $project->client, " |")
                : (string) $projectId;

            foreach ((array) $projectWorkLogs as $entryIndex => $entry) {
                if (!is_array($entry)) {
                    continue;
                }

                foreach (['type', 'start', 'end'] as $field) {
                    if (!array_key_exists($field, $entry) || trim((string) ($entry[$field] ?? '')) === '') {
                        $messages["work_logs.$projectId.$entryIndex.$field.required"] = __('log_validation.messages.required', [
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
        $workLogs = collect((array) $this->input('work_logs', []))
            ->map(function (array $projectWorkLogs) {
                return collect($projectWorkLogs)
                    ->filter(fn($entry) => is_array($entry))
                    ->filter(fn(array $entry) =>
                        trim((string) ($entry['start'] ?? '')) !== ''
                        || trim((string) ($entry['end'] ?? '')) !== ''
                        || trim((string) ($entry['comment'] ?? '')) !== '')
                    ->values()
                    ->all();
            })
            ->filter(fn(array $projectWorkLogs) => !empty($projectWorkLogs))
            ->all();

        $this->merge(['work_logs' => $workLogs]);
    }
}
