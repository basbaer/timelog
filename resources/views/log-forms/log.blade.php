<!doctype html>
<html lang="de">

@include('partials.head', ['title' => 'Log'])
<!-- configure class .harvester and .rueckezug -->

<body>
    @include('partials.log_header', ['name' => $worker->full_name, 'worker_id' => $worker->id])

    @include('partials.log_form_errors', ['errors' => $errors])

    @php
        $prefill = $prefill ?? [];
        $editingProjectId = $editingProjectId ?? null;
        $workTypes = [
            'motorsage' => __('form.motorsage'),
            'freischneider' => __('form.freischneider'),
            'seilmaschine' => __('form.seilmaschine'),
            'messkluppe' => __('form.messkluppe'),
            'reparatur' => __('form.reparatur'),
            'other' => __('form.other'),
        ];
        $workTypeCount = count($workTypes);
    @endphp

    <div class="container">

        <!-- Date -->
        <div class="container my-3 px-0">
            <label for="date" class="form-label h3">Datum</label>
            <input id="date" name="date" class="form-control" type="date"
                value="{{ $date }}"
                @if ($worker->type === 'forstwirt') readonly @endif
                onchange="window.location.href = '{{ route('log.' . $worker->type, ['worker_id' => $worker->id]) }}?date=' + this.value" />
        </div>

        <div id="log-entries">
            @foreach ($existingLogs as $existingLog)
                @include('log-forms.partials.log-summary-item', ['savedLog' => $existingLog, 'workTypes' => $workTypes])
            @endforeach
        </div>


        <div class="container d-flex justify-content-center">
            @if ($worker->type === 'rueckezug')
                <button id="btn-add-rueckezug" class="rueckezug btn btn-primary my-3 me-3"
                    type="button">{{ __('form.add_rueckezug') }}</button>
            @elseif ($worker->type === 'harvester')
                <button id="btn-add-harvester" class="harvester btn btn-primary my-3 me-3"
                    type="button">{{ __('form.add_harvester') }}</button>
            @endif

            <button id="btn-add-forstwirt" class="btn btn-primary my-3"
                type="button">{{ __('form.add_forstwirt') }}</button>
        </div>
    </div>


    <template id="template-rueckezug-form">
        <x-rueckezug-form :projects="$projects" :user_id="$worker->id" />
    </template>

    <template id="template-harvester-form">
        <x-harvester-form :projects="$projects" :user_id="$worker->id" />
    </template>

    <template id="template-forstwirt-form">
        <x-forstwirt-form :projects="$projects" :workTypes="$workTypes" :user_id="$worker->id" />
    </template>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>
    <script>
        function addLogForm(type) {
            const template = document.getElementById(`template-${type}-form`);
            const addButton = document.getElementById(`btn-add-${type}`);

            if (!template || !addButton || addButton.disabled) {
                return;
            }

            const node = template.content.cloneNode(true);
            document.getElementById('log-entries').appendChild(node);
            addButton.disabled = true;

            const form = document.getElementById(`${type}-log-form`);

            // per-type post-insert setup
            if (type === 'forstwirt' && typeof initForstwirtForm === 'function') {
                initForstwirtForm(form);
            }

            if (type === 'rueckezug' && typeof initRueckezugForm === 'function') {
                initRueckezugForm(form);
            }
            if (type === 'harvester' && typeof initHarvesterForm === 'function') {
                initHarvesterForm(form);
            }

            initLogFormSubmit(form);
        }

        const rueckezugButton = document.getElementById('btn-add-rueckezug');
        const harvesterButton = document.getElementById('btn-add-harvester');
        const forstwirtButton = document.getElementById('btn-add-forstwirt');

        if (rueckezugButton) {
            rueckezugButton.addEventListener('click', () => addLogForm('rueckezug'));
        }

        if (harvesterButton) {
            harvesterButton.addEventListener('click', () => addLogForm('harvester'));
        }

        if (forstwirtButton) {
            forstwirtButton.addEventListener('click', () => addLogForm('forstwirt'));
        }

        function cancelLogForm(button) {
            const form = button.closest('form');
            const type = form.dataset.logType;

            form.remove();

            const addButton = document.getElementById(`btn-add-${type}`);
            if (addButton) {
                addButton.disabled = false;
            }
        }
    </script>

</body>

</html>
