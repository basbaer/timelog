<!doctype html>
<html lang="de">

@include('partials.head', ['title' => 'Log'])

<body>
    @include('partials.log_header', ['name' => $name])

    @include('partials.log_form_errors', ['errors' => $errors])

    <form id="harvester-log-form" class="container" method="POST" action="{{ route('log.harvester.store') }}">
        @csrf
       <input type="hidden" name="user_id" value="{{ $user_id }}">
        @php
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
            <input id="date" name="log_date" class="form-control" type="date" value="{{ old('log_date', $today) }}" />
        </div>

        <div class="accordion" id="accordionProjects">
            @foreach ($projects as $project)
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse{{ $loop->index }}" aria-expanded="false"
                            aria-controls="collapse{{ $loop->index }}">
                            {{ $project->location }} | {{ $project->date->format('m/Y') }} | {{ $project->client }}
                        </button>
                    </h2>
                    <div id="collapse{{ $loop->index }}" class="accordion-collapse collapse"
                        data-bs-parent="#accordionProjects">
                        <div class="accordion-body px-2">
                            <div class="mb-2">
                                <h3>Arbeitszeit Harvester</h3>

                                <div class="row d-flex mb-2">
                                    <div class="col-sm-auto col-6">
                                        <label for="start-{{ $loop->index }}"
                                            class="form-label">{{ __('form.from') }}</label>
                                        <input type="time" id="start-{{ $loop->index }}" class="form-control"
                                            name="work_logs[{{ $project->id }}][start]" lang="de-DE" step="900"
                                            value="{{ old("work_logs.$project->id.start") }}">
                                    </div>

                                    <div class="col-sm-auto col-6">
                                        <label for="end-{{ $loop->index }}"
                                            class="form-label">{{ __('form.to') }}</label>
                                        <input type="time" id="end-{{ $loop->index }}" class="form-control"
                                            name="work_logs[{{ $project->id }}][end]" lang="de-DE" step="900"
                                            value="{{ old("work_logs.$project->id.end") }}">
                                    </div>
                                </div>
                            </div>
                            <!-- Betriebsstunden -->
                            <div class="mb-2">
                                <h3>Betriebsstunden</h3>

                                <div class="row mb-3">
                                    <div class="col-5 pe-2 me-0">
                                        <label for="bs_start-{{ $loop->index }}"
                                            class="form-label">{{ __('form.from') }}</label>
                                        <input type="number" id="bs_start-{{ $loop->index }}" class="form-control"
                                            name="work_logs[{{ $project->id }}][bs_start]"
                                            value="{{ old("work_logs.$project->id.bs_start") }}"
                                            placeholder="Stand: {{ $projects[$project->id]['last_bs'] }}">
                                    </div>

                                    <div class="col-5 ps-1">
                                        <label for="bs_end-{{ $loop->index }}"
                                            class="form-label">{{ __('form.to') }}</label>
                                        <input type="number" id="bs_end-{{ $loop->index }}" class="form-control"
                                            name="work_logs[{{ $project->id }}][bs_end]"
                                            value="{{ old("work_logs.$project->id.bs_end") }}">
                                    </div>

                                    <div class="col-2 ps-0">
                                        <label for="bs_diff-{{ $loop->index }}" class="form-label">Diff.</label>
                                        <input type="text" id="bs_diff-{{ $loop->index }}" class="form-control"
                                            readonly name="work_logs[{{ $project->id }}][bs_diff]"
                                            value="{{ old("work_logs.$project->id.bs_diff") }}">
                                    </div>
                                </div>
                            </div>

                            <div class="mb-2">
                                <h3 class="mt-2">Festmeter</h3>
                                <div class="row">
                                    <div class="col-3">
                                        <label for="stueckzahl-{{ $loop->index }}"
                                            class="form-label">Stückzahl</label>
                                        <input type="number" id="stueckzahl-{{ $loop->index }}" class="form-control"
                                            name="work_logs[{{ $project->id }}][stueckzahl]"
                                            value="{{ old("work_logs.$project->id.stueckzahl") }}">
                                    </div>
                                    <div class="col-5">
                                        <label for="fm_gesamt-{{ $loop->index }}" class="form-label">Gesamt fm</label>
                                        <input type="number" id="fm_gesamt-{{ $loop->index }}" class="form-control"
                                            name="work_logs[{{ $project->id }}][fm_gesamt]" data-fm-before="{{ $projects[$project->id]['last_fm_total'] }}"
                                            value="{{ old("work_logs.$project->id.fm_gesamt") }}"
                                            placeholder="Stand: {{ $projects[$project->id]['last_fm_total'] }}">
                                    </div>
                                    <div class="col-4">
                                        <label for="fm_day-{{ $loop->index }}" class="form-label">fm/Tag</label>
                                        <input type="number" id="fm_day-{{ $loop->index }}" class="form-control"
                                            readonly name="work_logs[{{ $project->id }}][fm_day]"
                                            value="{{ old("work_logs.$project->id.fm_day") }}">
                                    </div>

                                </div>
                            </div>

                            <hr class="mx-3">

                            <div class="mb-2">
                                <h3 class="mt-2">Weitere Arbeiten</h3>

                                <div id="forstwirt-work-entries-{{ $loop->index }}">
                                    @for ($entryIndex = 0; $entryIndex < $workTypeCount; $entryIndex++)
                                        <x-forstwirt-work-type :project-index="$loop->index" :project-id="$project->id"
                                            :entry-index="$entryIndex" :work-types="$workTypes"
                                            :hidden="$entryIndex > 0" />
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

        <div class="container d-flex justify-content-center">
            <button class="btn btn-success my-3" type="submit">Eintrag speichern</button>
        </div>
    </form>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
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

        function calculateFmDay(projectIndex) {
            const fmInput = document.getElementById(`fm_gesamt-${projectIndex}`);
            const fmdayInput = document.getElementById(`fm_day-${projectIndex}`);

            if (!fmInput || !fmdayInput) {
                return;
            }

            if (fmInput.value.trim() === "") {
                fmdayInput.value = "";
                return;
            }

            const fm = parseFloat(fmInput.value);
            // get the fm_before value from a data attribute on the fm input (set in the blade template)
            const fm_before = parseFloat(fmInput.dataset.fmBefore) || 0;
            const fm_day = fm - fm_before;

            fmdayInput.value = fm_day.toFixed(2);
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

            document.querySelectorAll('[id^="fm_gesamt-"]').forEach(fmInput => {
                const projectIndex = fmInput.id.substring("fm_gesamt-".length);

                fmInput.addEventListener("input", () => calculateFmDay(projectIndex));
                calculateFmDay(projectIndex);
            });
        });
    </script>
</body>

</html>