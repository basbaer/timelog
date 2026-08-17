{{-- resources/views/log-forms/forstwirt-form.blade.php --}}
@props(['projects', 'workTypes' => [], 'prefill' => []])

<form id="forstwirt-log-form" class="container border rounded-2 position-relative" method="POST"
    action="{{ route('log.forstwirt.store') }}" data-log-type="forstwirt">
    @csrf

    <div class="d-flex flex-row justify-content-between mb-1 mt-2">
        <label for="project_id" class="h3 form-label">{{ __('form.project') }}</label>
        <button type="button" class="btn-close" aria-label="{{ __('form.cancel') }}"
            onclick="cancelLogForm(this)"></button>
    </div>

    <div class="row">
        <div class="col-10 col-md-5 mb-3">
            <select id="forstwirt_project_id" name="project_id" class="form-select" required>
                <option value="" selected disabled>{{ __('form.select_project') }}</option>
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}"
                        {{ (string) old('project_id') === (string) $project->id ? 'selected' : '' }}>
                        {{ $project->location }} | {{ $project->date->format('m/Y') }} | {{ $project->client }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Work type -->

    <label for="work_type" class="form-label mb-3 h3">{{ __('form.working_type') }}</label>
    <div class="row mb-3">
        <div class="col-8 col-md-5">
            <select id="work_type" name="work_type" class="form-select" required>
                <option value="" selected disabled>{{ __('form.select_work_type') }}</option>
                @foreach ($workTypes as $type => $label)
                    <option value="{{ $type }}" {{ old('work_type') === $type ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Time / duration -->
    <div class="row mb-2">
        <div class="col-6 col-md-3 mb-2">
            <label for="start" class="form-label">{{ __('form.from') }}</label>
            <input type="time" id="start" class="form-control" name="start" lang="de-DE" step="900"
                value="{{ old('start', data_get($prefill, 'start')) }}">
        </div>

        <div class="col-6 col-md-3 mb-2">
            <label for="end" class="form-label">{{ __('form.to') }}</label>
            <input type="time" id="end" class="form-control" name="end" lang="de-DE" step="900"
                value="{{ old('end', data_get($prefill, 'end')) }}">
        </div>

        <div class="col-6 col-md-3 mb-2">
            <label for="pause" class="form-label">{{ __('form.pause') }}</label>
            <input type="number" id="pause" class="form-control" name="pause" min="0" step="15"
                value="{{ old('pause', data_get($prefill, 'pause', 0)) }}">
        </div>

        <div class="col-6 col-md-3 mb-2">
            <label for="sum" class="form-label">{{ __('form.working_time') }}</label>
            <input type="text" id="sum" class="form-control" name="sum" readonly
                value="{{ old('sum', data_get($prefill, 'sum')) }}">
        </div>
    </div>

    <!-- Comment -->
    <div class="mb-3">
        <label for="comment" class="form-label">{{ __('form.comment') }}</label>
        <textarea class="form-control" id="comment" name="comment" rows="3">{{ old('comment', data_get($prefill, 'comment')) }}</textarea>
    </div>

    <div class="container d-flex justify-content-center">
        <button class="btn btn-success my-3 me-3" type="submit">{{ __('form.submit') }}</button>
    </div>
</form>

<script>
    // Konvertiert eine Anzahl Minuten in einen HH:MM-String (z.B. 90 -> "01:30").
    function formatMinutesToHHMM(minutes) {
        const safeMinutes = Math.max(0, Math.round(minutes));
        const hours = Math.floor(safeMinutes / 60);
        const remainingMinutes = safeMinutes % 60;
        return `${String(hours).padStart(2, "0")}:${String(remainingMinutes).padStart(2, "0")}`;
    }

    function calculateForstwirtSum(form) {
        const startInput = form.querySelector('#start');
        const endInput = form.querySelector('#end');
        const pauseInput = form.querySelector('#pause');
        const sumInput = form.querySelector('#sum');

        if (!startInput || !endInput || !pauseInput || !sumInput) {
            return;
        }

        const start = startInput.value;
        const end = endInput.value;
        const pause = parseInt(pauseInput.value || 0, 10);

        if (!start || !end) {
            sumInput.value = "";
            return;
        }

        const startDate = new Date(`1970-01-01T${start}:00`);
        const endDate = new Date(`1970-01-01T${end}:00`);
        let diff = (endDate - startDate) / 60000;

        if (diff < 0) {
            diff += 24 * 60; // over midnight
        }

        diff -= pause;
        if (diff < 0) {
            diff = 0;
        }

        sumInput.value = formatMinutesToHHMM(diff);
    }

    function initForstwirtForm(form) {
        ["start", "end", "pause"].forEach(field => {
            const input = form.querySelector(`#${field}`);
            if (input) {
                input.addEventListener("input", () => calculateForstwirtSum(form));
            }
        });
    }

    window.initForstwirtForm = initForstwirtForm;
</script>
