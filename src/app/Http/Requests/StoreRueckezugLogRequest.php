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
            'log_date' => ['required', 'date'],
            'work_logs' => ['required', 'array', 'min:1'],
            'work_logs.*' => ['required', 'array'],
            'work_logs.*.start' => ['required', 'date_format:H:i'],
            'work_logs.*.end' => ['required', 'date_format:H:i'],
            'work_logs.*.sum' => ['nullable', 'date_format:H:i'],
            'work_logs.*.bs_from' => ['nullable', 'numeric'],
            'work_logs.*.bs_to' => ['nullable', 'numeric'],
            'work_logs.*.bs_diff' => ['nullable', 'numeric'],
            'work_logs.*.loadings' => ['nullable', 'numeric'],
            'work_logs.*.average_distance' => ['nullable', 'numeric'],
        ];
    }
}