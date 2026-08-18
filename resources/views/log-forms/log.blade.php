<!doctype html>
<html lang="de">

@include('partials.head', ['title' => 'Log'])
<!-- configure class .harvester and .rueckezug -->

<body>
    @include('partials.log_header', ['name' => $name, 'worker_id' => $user_id])

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
        $today = now()->format('Y-m-d');
    @endphp

    <form id="rueckezug-log-form" class="container" method="POST" action="{{ route('log.rueckezug.store') }}">
        @csrf
        <input type="hidden" name="user_id" value="{{ $user_id }}">
        @if (!empty($editingLogId))
            <input type="hidden" name="edit_log_id" value="{{ $editingLogId }}">
        @endif
        @if (!empty($editingLogDate))
            <input type="hidden" name="edit_log_date" value="{{ $editingLogDate }}">
        @endif

        <!-- Date -->
        <div class="container my-3 px-0">
            <label for="date" class="form-label h3">Datum</label>
            <input id="date" name="log_date" class="form-control" type="date"
                value="{{ old('log_date', data_get($prefill, 'log_date', $today)) }}" />
        </div>

        @if ($workerType !== 'forstwirt')
            <!-- Gesamtarbeitszeit -->
            <div class="row mb-2">
                <div class="h3">Gesamtarbeitszeit</div>
                <div class="col-6 col-md-3 mb-2">
                    <label for="total_start" class="form-label">{{ __('form.from') }}</label>
                    <input type="time" id="total_start" class="form-control" name="total_start" lang="de-DE"
                        step="900" value="{{ old('total_start', data_get($prefill, 'total_start')) }}">
                </div>

                <div class="col-6 col-md-3 mb-2">
                    <label for="total_end" class="form-label">{{ __('form.to') }}</label>
                    <input type="time" id="total_end" class="form-control" name="total_end" lang="de-DE"
                        step="900" value="{{ old('total_end', data_get($prefill, 'total_end')) }}">
                </div>

                <div class="col-6 col-md-3 mb-2">
                    <label for="total_pause" class="form-label">{{ __('form.pause') }}</label>
                    <input type="number" id="total_pause" class="form-control" name="total_pause" min="0"
                        step="15" value="{{ old('total_pause', data_get($prefill, 'total_pause', 0)) }}">
                </div>

                <div class="col-6 col-md-3 mb-2">
                    <label for="total_sum" class="form-label">{{ __('form.working_time') }}</label>
                    <input type="text" id="total_sum" class="form-control" name="total_sum" readonly
                        value="{{ old('total_sum', data_get($prefill, 'total_sum')) }}">
                </div>
            </div>
        @endif
        <!-- TODO: Check if any other field is set -->

        <!-- TODO: Load prev saved logs -->

        <div id="log-entries">
            <!-- summary cards + at most one open form live here -->
        </div>


        <div class="container d-flex justify-content-center">
            @if ($workerType === 'rueckezug')
                <button id="btn-add-rueckezug" class="rueckezug btn btn-primary my-3 me-3"
                    type="button">{{ __('form.add_rueckezug') }}</button>
            @elseif ($workerType === 'harvester')
                <button id="btn-add-harvester" class="harvester btn btn-primary my-3 me-3"
                    type="button">{{ __('form.add_harvester') }}</button>
            @endif

            <button id="btn-add-forstwirt" class="btn btn-primary my-3"
                type="button">{{ __('form.add_forstwirt') }}</button>
        </div>
    </form>


    <template id="template-rueckezug-form">
        <x-rueckezug-form :projects="$projects" />
    </template>

    <template id="template-harvester-form">
        <x-harvester-form :projects="$projects" />
    </template>

    <template id="template-forstwirt-form">
        <x-forstwirt-form :projects="$projects" :workTypes="$workTypes" />
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

        // Konvertiert eine Anzahl Minuten in einen HH:MM-String (z.B. 90 -> "01:30").
        function formatMinutesToHHMM(minutes) {
            const safeMinutes = Math.max(0, Math.round(minutes));
            const hours = Math.floor(safeMinutes / 60);
            const remainingMinutes = safeMinutes % 60;
            return `${String(hours).padStart(2, "0")}:${String(remainingMinutes).padStart(2, "0")}`;
        }

        function calculateTotalWorkingHours() {
            const startInput = document.getElementById('total_start');
            const endInput = document.getElementById('total_end');
            const pauseInput = document.getElementById('total_pause');
            const sumInput = document.getElementById('total_sum');

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

        document.addEventListener('DOMContentLoaded', () => {
            ['total_start', 'total_end', 'total_pause'].forEach(id => {
                const input = document.getElementById(id);
                if (input) {
                    input.addEventListener('input', calculateTotalWorkingHours);
                }
            });

            calculateTotalWorkingHours(); // in case of old()/prefill values on load
        });
    </script>

</body>

</html>
