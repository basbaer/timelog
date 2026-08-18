@props(['prefill' => [], 'projects'])

<form id="harvester-log-form" class="container border rounded-2 mt-2" method="POST"
    action="{{ route('log.harvester.store') }}" data-log-type="harvester">
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
        <h3 class="mt-2">Festmeter</h3>
        <div class="row">
            <div class="col-6 col-md-3">
                <!-- fm_amout is the ongoing Stueckzahl number at the end of the day -->
                <label for="fm_amount" class="form-label">Stückzahl</label>
                <input type="number" id="fm_amount" class="form-control" name="fm_amount"
                    value="{{ data_get($prefill, 'fm_amount') }}" data-last-fm-amount="{{ $lastFmAmount ?? 0 }}">
            </div>
            <div class="col-6 col-md-3">
                <label for="pieces_day" class="form-label">Stückzahl heute</label>
                <input readonly  id="pieces_day" class="form-control" name="pieces_day"
                    value="{{ data_get($prefill, 'pieces_day') }}">
            </div>
            <div class="col-6 col-md-3 mt-2 mt-md-0">
                <label for="fm_total" class="form-label">Festmeter</label>
                <input type="number" id="fm_total" class="form-control" name="fm_total" inputmode="decimal"
                    value="{{ data_get($prefill, 'fm_total') }}">
                <!-- TODO; add: placeholder="Stand: { $projects[$project->id]['last_fm_total'] }}" -->
            </div>
            <div class="col-6 col-md-3 mt-2 mt-md-0">
                <label for="fm_day" class="form-label">Festmeter heute</label>
                <input readonly id="fm_day" class="form-control" name="fm_day"
                    value="{{ data_get($prefill, 'fm_day') }}">
                <!-- TODO; add: placeholder="Stand: { $projects[$project->id]['last_fm_total'] }}" -->
            </div>
        </div>
    </div>
    <div class="container d-flex justify-content-center">
        <button class="btn btn-success my-3 me-3" type="submit">{{ __('form.submit') }}</button>
    </div>
</form>

<script>
    function calculateHarvesterDiff(form) {
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

    function calculateHarvesterPiecesOfDay(form) {
        const fmAmountInput = form.querySelector('#fm_amount');
        const piecesDayInput = form.querySelector('#pieces_day');

        if (!fmAmountInput || !piecesDayInput) {
            return;
        }

        const fmAmount = parseInt(fmAmountInput.value, 10);
        const lastFmAmount = parseInt(fmAmountInput.getAttribute('data-last-fm-amount'), 10) || 0;

        if (isNaN(fmAmount)) {
            piecesDayInput.value = "";
            return;
        }

        const piecesOfDay = fmAmount - lastFmAmount;
        piecesDayInput.value = piecesOfDay >= 0 ? piecesOfDay : 0;
    }

    function calculateHarvesterFestmeter(form) {
        const fmTotalInput = form.querySelector('#fm_total');
        const fmDayInput = form.querySelector('#fm_day');

        if (!fmTotalInput || !fmDayInput) {
            return;
        }

        const fmTotal = parseFloat(fmTotalInput.value);
        const lastFmTotal = parseFloat(fmTotalInput.getAttribute('data-last-fm-total')) || 0;

        if (isNaN(fmTotal)) {
            fmDayInput.value = "";
            return;
        }

        const fmDay = fmTotal - lastFmTotal;
        fmDayInput.value = fmDay >= 0 ? fmDay.toFixed(2) : 0;
    }

    // updates the data-last-fm-amount attribute of the fm_amount input based on the selected project
    // in order to calculate the pieces of day correctly
    function updateLastFmAmount(form, projectId) {
        const fmAmountInput = form.querySelector('#fm_amount');
        // Get last_fm_amount form projects data passed from the controller
        // data is under projects[projectId]['last_fm_amount']
        const $projects = @json($projects);
        const lastAmount = $projects[projectId]['last_fm_amount'] || 0;
        fmAmountInput.setAttribute('data-last-fm-amount', lastAmount);
    }

    function updateLastFmTotal(form, projectId) {
        const fmTotalInput = form.querySelector('#fm_total');
        // Get last_fm_total form projects data passed from the controller
        // data is under projects[projectId]['last_fm_total']
        const $projects = @json($projects);
        const lastTotal = $projects[projectId]['last_fm_total'] || 0;
        fmTotalInput.setAttribute('data-last-fm-total', lastTotal);
    }

    function initHarvesterForm(form) {
        ["bs_start", "bs_end"].forEach(field => {
            const input = form.querySelector(`#${field}`);
            if (input) {
                input.addEventListener("input", () => calculateHarvesterDiff(form));
            }
        });

        //Add event listener for project selection change to update last fm_amount
        const projectSelect = form.querySelector('#project_id');
        if (projectSelect) {
            projectSelect.addEventListener("change", () => {
                const selectedOption = projectSelect.options[projectSelect.selectedIndex];
                // Send project id to function that updates the last fm_amount based on the selected project
                const projectId = selectedOption.value;
                updateLastFmAmount(form, projectId);
                updateLastFmTotal(form, projectId);
            });
        }

        // Add event listeners for Festmeter fields
        const input = form.querySelector(`#fm_total`);
        if (input) {
            input.addEventListener("input", () => calculateHarvesterFestmeter(form));
        }


        // Add event listeners for Pieces of Day field
        const piecesDayInput = form.querySelector('#fm_amount');
        if (piecesDayInput) {
            piecesDayInput.addEventListener("input", () => calculateHarvesterPiecesOfDay(form));
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

    window.initHarvesterForm = initHarvesterForm;
</script>
