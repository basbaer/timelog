@props([
    'projectIndex',
    'entryIndex',
    'workTypes' => [],
])

@php
    $entryBase = "work-type-{$projectIndex}-{$entryIndex}";
@endphp

<div class="work-type-entry" data-project-index="{{ $projectIndex }}" data-entry-index="{{ $entryIndex }}">
    <div class="row mb-2">
        <div class="col-6">
            <label for="{{ $entryBase }}-type" class="form-label">
                <strong>{{ __('form.working_type') }}</strong>
            </label>
            <select id="{{ $entryBase }}-type" class="form-select work-type-select"
                name="work_logs[{{ $projectIndex }}][entries][{{ $entryIndex }}][type]"
                data-project-index="{{ $projectIndex }}" data-entry-index="{{ $entryIndex }}">
                @foreach ($workTypes as $type => $label)
                    <option value="{{ $type }}">{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-6">
            <label for="{{ $entryBase }}-hours" class="form-label">{{ __('form.hours') }}</label>
            <input type="time" id="{{ $entryBase }}-hours" class="form-control"
                name="work_logs[{{ $projectIndex }}][entries][{{ $entryIndex }}][hours]" step="900">
        </div>
    </div>

    <div class="mb-3">
        <label for="{{ $entryBase }}-comment" class="form-label">{{ __('form.comment') }}</label>
        <textarea class="form-control" id="{{ $entryBase }}-comment"
            name="work_logs[{{ $projectIndex }}][entries][{{ $entryIndex }}][comment]" rows="3"></textarea>
    </div>

    <hr class="mx-3">
</div>
