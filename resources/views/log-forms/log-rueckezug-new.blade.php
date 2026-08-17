<!doctype html>
<html lang="de">

@include('partials.head', ['title' => 'Log'])

<body>
    @include('partials.log_header', ['name' => $name, 'worker_id' => $user_id])

    @include('partials.log_form_errors', ['errors' => $errors])

    <form id="rueckezug-log-form" class="container" method="POST" action="{{ route('log.rueckezug.store') }}">
        @csrf
        <input type="hidden" name="user_id" value="{{ $user_id }}">
        @if (!empty($editingLogId))
            <input type="hidden" name="edit_log_id" value="{{ $editingLogId }}">
        @endif
        @if (!empty($editingLogDate))
            <input type="hidden" name="edit_log_date" value="{{ $editingLogDate }}">
        @endif
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
        <!-- Date -->
        <div class="container my-3 px-0">
            <label for="date" class="form-label">Datum</label>
            <input id="date" name="log_date" class="form-control" type="date"
                value="{{ old('log_date', data_get($prefill, 'log_date', $today)) }}" />
        </div>


        <!-- Gesamtarbeitszeit -->
        <!-- TODO: Check if any other field is set -->

        <!-- TODO: Load prev saved logs -->

        <div class="accordion mb-3" id="accordionProjects">
            @foreach ($projects as $project)
                @php($isEditingProject = (int) $editingProjectId === (int) $project->id)
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button @if (!$isEditingProject) collapsed @endif"
                            type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{ $loop->index }}"
                            aria-expanded="{{ $isEditingProject ? 'true' : 'false' }}"
                            aria-controls="collapse{{ $loop->index }}">
                            {{ $project->location }} | {{ $project->date->format('m/Y') }} | {{ $project->client }}
                        </button>
                    </h2>
                    <div id="collapse{{ $loop->index }}"
                        class="accordion-collapse collapse @if ($isEditingProject) show @endif"
                        data-bs-parent="#accordionProjects">
                        <div class="accordion-body px-2">
                            <!-- Betriebsstunden -->
                            <div class="mb-2">
                                <h3>Betriebsstunden</h3>

                                <div class="row mb-3">
                                    <div class="col-5 pe-2 me-0">
                                        <label for="bs_start-{{ $loop->index }}"
                                            class="form-label">{{ __('form.from') }}</label>
                                        <input type="number" id="bs_start-{{ $loop->index }}" class="form-control"
                                            name="work_logs[{{ $project->id }}][bs_start]" step="0.01"
                                            inputmode="decimal"
                                            value="{{ old("work_logs.$project->id.bs_start", data_get($prefill, "work_logs.$project->id.bs_start")) }}"
                                            placeholder="Letzer Stand dieses Projekt: {{ $projects[$project->id]['last_bs'] }}">
                                    </div>

                                    <div class="col-5 ps-1">
                                        <label for="bs_end-{{ $loop->index }}"
                                            class="form-label">{{ __('form.to') }}</label>
                                        <input type="number" id="bs_end-{{ $loop->index }}" class="form-control"
                                            name="work_logs[{{ $project->id }}][bs_end]" step="0.01"
                                            inputmode="decimal"
                                            value="{{ old("work_logs.$project->id.bs_end", data_get($prefill, "work_logs.$project->id.bs_end")) }}">
                                    </div>

                                    <div class="col-2 ps-0">
                                        <label for="bs_diff-{{ $loop->index }}" class="form-label">Diff.</label>
                                        <input type="text" id="bs_diff-{{ $loop->index }}" class="form-control"
                                            readonly name="work_logs[{{ $project->id }}][bs_diff]"
                                            value="{{ old("work_logs.$project->id.bs_diff", data_get($prefill, "work_logs.$project->id.bs_diff")) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-2">
                                <h3>Gesamtarbeitszeit (inkl. Forstwirtarbeiten)</h3>

                                <div class="row d-flex mb-2">
                                    <div class="col-sm-auto col-6">
                                        <label for="start-{{ $loop->index }}"
                                            class="form-label">{{ __('form.from') }}</label>
                                        <input type="time" id="start-{{ $loop->index }}" class="form-control"
                                            name="work_logs[{{ $project->id }}][start]" lang="de-DE" step="900"
                                            value="{{ old("work_logs.$project->id.start", data_get($prefill, "work_logs.$project->id.start")) }}">
                                    </div>

                                    <div class="col-sm-auto col-6">
                                        <label for="end-{{ $loop->index }}"
                                            class="form-label">{{ __('form.to') }}</label>
                                        <input type="time" id="end-{{ $loop->index }}" class="form-control"
                                            name="work_logs[{{ $project->id }}][end]" lang="de-DE" step="900"
                                            value="{{ old("work_logs.$project->id.end", data_get($prefill, "work_logs.$project->id.end")) }}">
                                    </div>

                                    <div class="col-sm-auto col-6">
                                        <label for="pause-{{ $loop->index }}"
                                            class="form-label">{{ __('form.pause') }}</label>
                                        <input type="number" id="pause-{{ $loop->index }}" class="form-control"
                                            name="work_logs[{{ $project->id }}][pause]" min="0" step="15"
                                            value="{{ old("work_logs.$project->id.pause", data_get($prefill, "work_logs.$project->id.pause")) }}"
                                            placeholder="0">
                                    </div>

                                    <div class="col-sm-auto col-6">
                                        <label for="sum-{{ $loop->index }}"
                                            class="form-label">{{ __('form.working_time') }}</label>
                                        <input type="text" id="sum-{{ $loop->index }}" class="form-control"
                                            name="work_logs[{{ $project->id }}][sum]" readonly
                                            value="{{ old("work_logs.$project->id.sum", data_get($prefill, "work_logs.$project->id.sum")) }}">
                                    </div>


                                </div>
                            </div>
                            <div class="mb-2">
                                <h3 class="mt-2">Ladungen</h3>
                                <div class="row">
                                    <div class="col-3">
                                        <label for="loadings-{{ $loop->index }}" class="form-label">Fuhren</label>
                                        <input type="number" id="loadings-{{ $loop->index }}" class="form-control"
                                            name="work_logs[{{ $project->id }}][loadings]"
                                            value="{{ old("work_logs.$project->id.loadings", data_get($prefill, "work_logs.$project->id.loadings")) }}">
                                    </div>
                                    <div class="col-7 col-lg-3">
                                        <label for="average_distance-{{ $loop->index }}"
                                            class="form-label">durchschnittliche Distanz (m)</label>
                                        <input type="number" id="average_distance-{{ $loop->index }}"
                                            class="form-control"
                                            name="work_logs[{{ $project->id }}][average_distance]"
                                            data-fm-before="{{ $projects[$project->id]['last_average_distance'] }}"
                                            step="0.01" inputmode="decimal"
                                            value="{{ old("work_logs.$project->id.average_distance", data_get($prefill, "work_logs.$project->id.average_distance")) }}"
                                            placeholder="Stand: {{ $projects[$project->id]['last_average_distance'] }}">
                                    </div>
                                </div>
                            </div>

                            <hr class="mx-3">

                            <div class="mb-2">
                                <h3 class="mt-2">Weitere Arbeiten</h3>

                                <div id="forstwirt-work-entries-{{ $loop->index }}">
                                    @for ($entryIndex = 0; $entryIndex < $workTypeCount; $entryIndex++)
                                        <x-forstwirt-work-type :project-index="$loop->index" :project-id="$project->id" :entry-index="$entryIndex"
                                            :work-types="$workTypes" :hidden="$entryIndex > 0" :prefill="$prefill" />
                                    @endfor
                                </div>

                                <div class="mt-2 mx-3">
                                    <button id="add-work-type-button-{{ $loop->index }}" class="btn btn-primary"
                                        type="button" data-project-index="{{ $loop->index }}"
                                        onclick="showWorkTypeEntry({{ $loop->index }}, this)">
                                        {{ __('form.add_work_type') }}
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div id="log-entries">
            <!-- summary cards + at most one open form live here -->
        </div>


        <div class="container d-flex justify-content-center">
            <button id="btn-add-rueckezug" class="btn btn-primary my-3 me-3"
                type="button">{{ __('form.add_rueckezug') }}</button>
            <button id="btn-add-forstwirt" class="btn btn-primary my-3"
                type="button">{{ __('form.add_forstwirt') }}</button>
        </div>
    </form>


    <template id="template-rueckezug-form">
        <x-rueckezug-form :projects="$projects" />
    </template>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>
    <script>
        document.getElementById('btn-add-rueckezug').addEventListener('click', () => {
            const template = document.getElementById('template-rueckezug-form');
            const node = template.content.cloneNode(true);
            document.getElementById('log-entries').appendChild(node);
            document.getElementById('btn-add-rueckezug').disabled = true;
        });

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
    <script>
        function calculateDiff(projectIndex) {
            const startInput = document.getElementById(`bs_start-${projectIndex}`);
            const endInput = document.getElementById(`bs_end-${projectIndex}`);
            const diffInput = document.getElementById(`bs_diff-${projectIndex}`);

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

        function formatHarvesterMinutesToHHMM(minutes) {
            const safeMinutes = Math.max(0, Math.round(minutes));
            const hours = Math.floor(safeMinutes / 60);
            const remainingMinutes = safeMinutes % 60;

            return `${String(hours).padStart(2, "0")}:${String(remainingMinutes).padStart(2, "0")}`;
        }

        function calculateHarvesterSum(projectIndex) {
            const startInput = document.getElementById(`start-${projectIndex}`);
            const endInput = document.getElementById(`end-${projectIndex}`);
            const pauseInput = document.getElementById(`pause-${projectIndex}`);
            const sumInput = document.getElementById(`sum-${projectIndex}`);

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
                diff += 24 * 60;
            }

            diff -= pause;
            if (diff < 0) {
                diff = 0;
            }

            sumInput.value = formatHarvesterMinutesToHHMM(diff);
        }

        document.addEventListener("DOMContentLoaded", function() {
            initForstwirtWorkTypeEntries();

            document.querySelectorAll('[id^="bs_start-"]').forEach(startInput => {
                const projectIndex = startInput.id.substring("bs_start-".length);
                calculateDiff(projectIndex);
            });

            document.querySelectorAll('[id^="bs_start-"]').forEach(startInput => {
                const projectIndex = startInput.id.substring("bs_start-".length);

                ["bs_start", "bs_end"].forEach(field => {
                    const input = document.getElementById(`${field}-${projectIndex}`);
                    if (input) {
                        input.addEventListener("input", () => calculateDiff(projectIndex));
                    }
                });
            });

            document.querySelectorAll('[id^="start-"]').forEach(startInput => {
                const projectIndex = startInput.id.substring("start-".length);

                ["start", "end", "pause"].forEach(field => {
                    const input = document.getElementById(`${field}-${projectIndex}`);
                    if (input) {
                        input.addEventListener("input", () => calculateHarvesterSum(projectIndex));
                    }
                });

                calculateHarvesterSum(projectIndex);
            });

        });
    </script>
</body>

</html>
