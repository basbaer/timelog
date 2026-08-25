@props(['prefill' => ['bs_start' => ''], 'projects', 'worker_id'])

<form id="rueckezug-log-form" class="container border rounded-2 mt-2" method="POST"
    action="{{ route('log.rueckezug.store') }}" data-log-type="rueckezug">
    @csrf
    <input type="hidden" name="worker_id" value="{{ $worker_id }}">

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

    <!-- Arbeitszeit -->
    <div class="row mb-2">
        <div class="h3">{{ __('form.working_time') }}</div>
        <div class="col-6 col-md-3 mb-2">
            <label for="start" class="form-label">{{ __('form.from') }}</label>
            <input type="time" id="start" class="form-control" name="start" lang="de-DE" step="900"
                value="{{ data_get($prefill, 'start') }}">
        </div>

        <div class="col-6 col-md-3 mb-2">
            <label for="end" class="form-label">{{ __('form.to') }}</label>
            <input type="time" id="end" class="form-control" name="end" lang="de-DE" step="900"
                value="{{ data_get($prefill, 'end') }}">
        </div>

        <div class="col-6 col-md-3 mb-2">
            <label for="pause" class="form-label">{{ __('form.pause') }}</label>
            <input type="number" id="pause" class="form-control" name="pause" min="0" step="15"
                value="{{ data_get($prefill, 'pause') }}">
        </div>

        <div class="col-6 col-md-3 mb-2">
            <label for="sum" class="form-label">{{ __('form.working_time') }}</label>
            <input type="text" id="sum" class="form-control" name="sum" readonly
                value="{{ data_get($prefill, 'sum') }}">
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

    function updateBsPlacholder(form, projectId) {
        const bsStartInput = form.querySelector('#bs_start');
        // Get last_bs form projects data passed from the controller
        // data is under projects[projectId]['last_bs']
        const $projects = @json($projects);
        const lastBs = $projects[projectId]['last_bs'] || 0;
        bsStartInput.setAttribute('placeholder', `Letzer Stand: ${lastBs}`);
    }

    function updateLastAverageDistance(form, projectId) {
        const averageDistanceInput = form.querySelector('#average_distance');
        // Get last_average_distance form projects data passed from the controller
        // data is under projects[projectId]['last_average_distance']
        const $projects = @json($projects);
        const lastAverageDistance = $projects[projectId]['last_average_distance'] || 0;
        averageDistanceInput.setAttribute('placeholder', `Letzer Stand: ${lastAverageDistance}`);
    }

    // Konvertiert eine Anzahl Minuten in einen HH:MM-String (z.B. 90 -> "01:30").
    function formatMinutesToHHMM(minutes) {
        const safeMinutes = Math.max(0, Math.round(minutes));
        const hours = Math.floor(safeMinutes / 60);
        const remainingMinutes = safeMinutes % 60;
        return `${String(hours).padStart(2, "0")}:${String(remainingMinutes).padStart(2, "0")}`;
    }

    function calculateWorkingHours() {
        const startInput = document.getElementById('start');
        const endInput = document.getElementById('end');
        const pauseInput = document.getElementById('pause');
        const sumInput = document.getElementById('sum');

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


    function initRueckezugForm(form) {
        ["bs_start", "bs_end"].forEach(field => {
            const input = form.querySelector(`#${field}`);
            if (input) {
                input.addEventListener("input", () => calculateRueckezugDiff(form));
            }
        });
        //TODO: for editin: add prop with project id
        // calculateRueckezugDiff(form); // in case of old()/prefill values on load

        ["start", "end", "pause"].forEach(field => {
            const input = form.querySelector(`#${field}`);
            if (input) {
                input.addEventListener("input", () => calculateWorkingHours());
            }
        });
        //calculateWorkingHours(); // in case of old()/prefill values on load

        //Add event listener for project selection change to update last fm_amount
        const projectSelect = form.querySelector('#project_id');
        if (projectSelect) {
            projectSelect.addEventListener("change", () => {
                const selectedOption = projectSelect.options[projectSelect.selectedIndex];
                // Send project id to function that updates the last fm_amount based on the selected project
                const projectId = selectedOption.value;
                updateLastAverageDistance(form, projectId);
                updateBsPlacholder(form, projectId);
            });
        }
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

    function initLogFormSubmit(form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();
            clearFormErrors(form);

            const submitButton = form.querySelector('button[type="submit"]');
            submitButton.disabled = true;

            // submitting the date from the date input of the side the form is rendered on
            const formData = new FormData(form);
            const dateInput = document.getElementById('date');
            formData.set('date', dateInput.value);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                    body: formData,
                });

                if (response.status === 422) {
                    const data = await response.json();
                    showFormErrors(form, data.errors);
                    submitButton.disabled = false;
                    return;
                }

                if (!response.ok) {
                    throw new Error(`Unexpected response: ${response.status}`);
                }

                const data = await response.json();
                const type = form.dataset.logType;

                form.outerHTML = data.html;
                document.getElementById(`btn-add-${type}`).disabled = false;
            } catch (err) {
                submitButton.disabled = false;
                showFormErrors(form, {
                    general: ['{{ __('form.save_error') }}']
                });
                console.error(err);
            }
        });

    }

    window.initLogFormSubmit = initLogFormSubmit;
    window.initRueckezugForm = initRueckezugForm;
</script>
