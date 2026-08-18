@props(['prefill' => ['bs_start' => ''], 'projects'])

<form id="rueckezug-log-form" class="container border rounded-2 mt-2" method="POST"
    action="{{ route('log.rueckezug.store') }}" data-log-type="rueckezug">
    @csrf

    <!-- Projekt Dropdown -->
    <div class="d-flex flex-row justify-content-between mb-1 mt-2">
        <label for="project_id" class="h3 form-label">{{ __('form.project') }}</label>
        <button type="button" class="btn-close" aria-label="{{ __('form.cancel') }}"
            onclick="cancelLogForm(this)"></button>
    </div>

    <div class="row">
        <div class="col-10 col-md-5 mb-3">
            <select id="project_id" name="project_id" class="form-select" required>
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

    <!-- Betriebsstunden -->
    <div class="my-2">
        <h3>Betriebsstunden</h3>

        <div class="row mb-3">
            <div class="col-5 pe-2 me-0">
                <label for="bs_start" class="form-label">{{ __('form.from') }}</label>
                <input type="number" id="bs_start" class="form-control" name="bs_start" step="0.01"
                    inputmode="decimal" value="{{ data_get($prefill, 'bs_start') }}">
                <!-- TODO: add: placeholder="Letzer Stand dieses Projekt: { $projects[$project->id]['last_bs'] }}" -->
            </div>

            <div class="col-5 ps-1">
                <label for="bs_end" class="form-label">{{ __('form.to') }}</label>
                <input type="number" id="bs_end" class="form-control" name="bs_end" step="0.01"
                    inputmode="decimal" value="{{ data_get($prefill, 'bs_end') }}">
            </div>

            <div class="col-2 ps-0">
                <label for="bs_diff" class="form-label">Diff.</label>
                <input type="text" id="bs_diff" class="form-control" readonly name="bs_diff"
                    value="{{ data_get($prefill, 'bs_diff') }}">
            </div>
        </div>
    </div>

    <div class="mb-2">
        <h3 class="mt-2">Ladungen</h3>
        <div class="row">
            <div class="col-3">
                <label for="loadings" class="form-label">Fuhren</label>
                <input type="number" id="loadings" class="form-control" name="loadings"
                    value="{{ data_get($prefill, 'loadings') }}">
            </div>
            <div class="col-7 col-lg-4">
                <label for="average_distance" class="form-label">durchschnittliche Distanz (m)</label>
                <input type="number" id="average_distance" class="form-control" name="average_distance"
                    inputmode="decimal" value="{{ data_get($prefill, 'average_distance') }}">
                <!-- TODO; add: placeholder="Stand: { $projects[$project->id]['last_average_distance'] }}" -->
            </div>
        </div>
    </div>
    <div class="container d-flex justify-content-center">
        <button class="btn btn-success my-3 me-3" type="submit">{{ __('form.submit') }}</button>
    </div>
</form>

<script>
    function calculateRueckezugDiff(form) {
        const startInput = form.querySelector('#bs_start');
        const endInput = form.querySelector('#bs_end');
        const diffInput = form.querySelector('#bs_diff');

        if (!startInput || !endInput || !diffInput) {
            return;
        }

        const start = startInput.value;
        const end = endInput.value;

        if (!start || !end) {
            diffInput.value = "";
            return;
        }

        const diff = parseFloat(end) - parseFloat(start);
        diffInput.value = diff.toFixed(2);
    }

    function initRueckezugForm(form) {
        ["bs_start", "bs_end"].forEach(field => {
            const input = form.querySelector(`#${field}`);
            if (input) {
                input.addEventListener("input", () => calculateRueckezugDiff(form));
            }
        });
    }

    function clearFormErrors(form) {
        const existing = form.querySelector('.form-errors-alert');
        if (existing) {
            existing.remove();
        }
    }

    function showFormErrors(form, errors) {
        clearFormErrors(form);

        const messages = Object.values(errors || {}).flat();
        if (!messages.length) {
            return;
        }

        const alert = document.createElement('div');
        alert.className = 'alert alert-danger form-errors-alert mt-2';
        alert.setAttribute('role', 'alert');

        const list = document.createElement('ul');
        list.className = 'mb-0 ps-3';
        messages.forEach(message => {
            const item = document.createElement('li');
            item.textContent = message;
            list.appendChild(item);
        });
        alert.appendChild(list);

        form.prepend(alert);
    }

    window.initRueckezugForm = initRueckezugForm;
</script>
