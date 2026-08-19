<?php

namespace App\Http\Requests;

use App\Models\ForstwirtWorkingType;
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
            'user_id' => ['required', 'integer', 'exists:users,id'],
            'project_id' => ['required', 'integer', 'exists:projects,id'],
            'date' => ['required', 'date'],
            'work_type' => ['required', 'string', Rule::in($workTypeKeys)],
            'start' => ['required', 'date_format:H:i'],
            'end' => ['required', 'date_format:H:i'],
            'pause' => ['nullable', 'integer', 'min:0'],
            'sum' => [],
            'comment' => ['nullable', 'string', 'max:1000'],
            'log_type' => ['nullable', 'string'],
        ];
    }

    public function attributes(): array
    {
        return [
            'date' => __('log_validation.fields.date'),
            'type' => __('log_validation.fields.type'),
            'start' => __('log_validation.fields.start'),
            'end' => __('log_validation.fields.end'),
            'pause' => __('log_validation.fields.pause'),
            'sum' => __('log_validation.fields.sum'),
            'comment' => __('log_validation.fields.comment'),
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['log_type' => 'forstwirt']);
    }

}
