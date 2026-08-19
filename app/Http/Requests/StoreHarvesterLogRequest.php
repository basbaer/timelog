<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreHarvesterLogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'date' => ['required', 'date'],

            'start' => ['required', 'nullable', 'date_format:H:i', 'required_with:end'],
            'end' => ['required', 'nullable', 'date_format:H:i', 'required_with:start'],
            'sum' => ['required', 'nullable', 'date_format:H:i'],
            'pause' => ['nullable', 'integer', 'min:0'],

            'bs_start' => ['nullable', 'numeric', 'required_with:bs_end'],
            'bs_end' => ['nullable', 'numeric', 'required_with:bs_start'],
            'bs_diff' => ['nullable', 'numeric'],

            'fm_amount' => ['nullable', 'numeric'],
            'fm_total' => ['nullable', 'numeric'],
            'fm_day' => ['nullable', 'numeric']
        ];
    }

    public function messages(): array
    {
        return [
            'start.required_with' => __('log_validation.messages.required_with', [
                'field' => __('log_validation.fields.start'),
                'other_field' => __('log_validation.fields.end'),
            ]),
            'end.required_with' => __('log_validation.messages.required_with', [
                'field' => __('log_validation.fields.end'),
                'other_field' => __('log_validation.fields.start'),
            ]),
            'bs_start.required_with' => __('log_validation.messages.required_with', [
                'field' => __('log_validation.fields.bs_start'),
                'other_field' => __('log_validation.fields.bs_end'),
            ]),
            'bs_end.required_with' => __('log_validation.messages.required_with', [
                'field' => __('log_validation.fields.bs_end'),
                'other_field' => __('log_validation.fields.bs_start'),
            ]),
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
    }

    public function attributes(): array
    {
        return [
            'start' => __('log_validation.fields.start'),
            'end' => __('log_validation.fields.end'),
            'sum' => __('log_validation.fields.sum'),
            'pause' => __('log_validation.fields.pause'),
            'bs_start' => __('log_validation.fields.bs_start'),
            'bs_end' => __('log_validation.fields.bs_end'),
            'bs_diff' => __('log_validation.fields.bs_diff'),
            'fm_amount' => __('log_validation.fields.fm_amount'),
            'fm_total' => __('log_validation.fields.fm_total'),
            'fm_day' => __('log_validation.fields.fm_day')
        ];
    }
}