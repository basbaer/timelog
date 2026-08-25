<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRueckezugLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'worker_id' => ['required', 'integer', 'exists:users,id'],
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'date' => ['required', 'date'],

            'start' => ['required', 'nullable', 'date_format:H:i', 'required_with:end'],
            'end' => ['nullable', 'date_format:H:i', 'required_with:start'],
            'sum' => ['nullable', 'date_format:H:i'],
            'pause' => ['nullable', 'integer', 'min:0'],

            'bs_start' => ['nullable', 'numeric', 'required_with:bs_end', "min:0"],
            'bs_end' => ['nullable', 'numeric', 'required_with:bs_start', "min:0"],
            'bs_diff' => ['nullable', 'numeric', 'min:0'],

            'loadings' => ['nullable', 'numeric', 'min:0'],
            'average_distance' => ['nullable', 'numeric', 'min:0'],
            'log_type' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'project_id' => __('log_validation.fields.project_id'),
            'date' => __('log_validation.fields.date'),
            'start' => __('log_validation.fields.start'),
            'end' => __('log_validation.fields.end'),
            'sum' => __('log_validation.fields.sum'),
            'pause' => __('log_validation.fields.pause'),
            'bs_start' => __('log_validation.fields.bs_start'),
            'bs_end' => __('log_validation.fields.bs_end'),
            'bs_diff' => __('log_validation.fields.bs_diff'),
            'loadings' => __('log_validation.fields.loadings'),
            'average_distance' => __('log_validation.fields.average_distance'),
        ];
    }

    protected function prepareForValidation(): void
    {
        $decimalFields = ['bs_start', 'bs_end', 'loadings', 'average_distance'];

        $normalized = [];
        foreach ($decimalFields as $field) {
            if ($this->filled($field)) {
                $normalized[$field] = str_replace(',', '.', (string) $this->input($field));
            }
        }

        if (!empty($normalized)) {
            $this->merge($normalized);
        }

        $this->merge([
            'log_type' => 'rueckezug',
        ]);
    }
}
