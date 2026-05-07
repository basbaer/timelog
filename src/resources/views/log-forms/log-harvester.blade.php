<!doctype html>
<html lang="de">

@include('partials.head', ['title' => 'Log'])

<body>
    @include('partials.log_header', ['name' => $name])

    @include('partials.log_form_errors', ['errors' => $errors])

    <form id="harvester-log-form" class="container" method="POST" action="{{ route('log.harvester.store') }}">
        @csrf
        <input type="hidden" name="id" value="{{ $id }}">
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
        @endphp
        <!-- Date -->
        <div class="container my-3 px-0">
            <label for="date" class="form-label">Datum</label>
            <input id="date" name="log_date" class="form-control" type="date" />
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const input = document.getElementById("date");
                    const today = new Date().toISOString().split("T")[0]; // YYYY-MM-DD
                    input.value = today;
                });
            </script>
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
                    <div id="collapse{{ $loop->index }}" class="accordion-collapse collapse "
                        data-bs-parent="#accordionProjects">
                        <div class="accordion-body px-2">

                            <input type="hidden" name="work_logs[{{ $loop->index }}][project_id]"
                                value="{{ $project->id }}">

                            <!-- Arbeitszeit -->
                            <div class="mb-2">
                                <h3>Arbeitszeit</h3>

                                <div class="row d-flex mb-2">
                                    <div class="col-sm-auto col-6">
                                        <label for="start-{{ $loop->index }}"
                                            class="form-label">{{ __('form.from') }}</label>
                                        <input type="time" id="start-{{ $loop->index }}" class="form-control"
                                            name="work_logs[{{ $loop->index }}][start]" lang="de-DE" step="900">
                                    </div>

                                    <div class="col-sm-auto col-6">
                                        <label for="end-{{ $loop->index }}"
                                            class="form-label">{{ __('form.to') }}</label>
                                        <input type="time" id="end-{{ $loop->index }}" class="form-control"
                                            name="work_logs[{{ $loop->index }}][end]" lang="de-DE" step="900">
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
                                            name="work_logs[{{ $loop->index }}][bs_start]">
                                    </div>

                                    <div class="col-5 ps-1">
                                        <label for="bs_end-{{ $loop->index }}"
                                            class="form-label">{{ __('form.to') }}</label>
                                        <input type="number" id="bs_end-{{ $loop->index }}" class="form-control"
                                            name="work_logs[{{ $loop->index }}][bs_end]">
                                    </div>

                                    <div class="col-2 ps-0">
                                        <label for="bs_diff-{{ $loop->index }}" class="form-label">Diff.</label>
                                        <input type="text" id="bs_diff-{{ $loop->index }}" class="form-control"
                                            readonly name="work_logs[{{ $loop->index }}][bs_diff]">
                                    </div>
                                </div>

                            </div>

                            <hr class="mx-3">

                            <!-- Festmeter -->
                            <div class="mb-2">
                                <h3 class="mt-2">Festmeter</h3>
                                <div class="row">
                                    <div class="col-4">
                                        <label for="stueckzahl-{{ $loop->index }}"
                                            class="form-label">Stückzahl</label>
                                        <input type="number" id="stueckzahl-{{ $loop->index }}" class="form-control"
                                            name="work_logs[{{ $loop->index }}][stueckzahl]">
                                    </div>
                                    <div class="col-4">
                                        <label for="fm_gesamt-{{ $loop->index }}" class="form-label">Gesamt fm</label>
                                        <input type="number" id="fm_gesamt-{{ $loop->index }}" class="form-control"
                                            name="work_logs[{{ $loop->index }}][fm_gesamt]">
                                    </div>
                                    <div class="col-4">
                                        <label for="day_fm-{{ $loop->index }}" class="form-label">fm/Tag</label>
                                        <input type="text" id="day_fm-{{ $loop->index }}" class="form-control"
                                            readonly name="work_logs[{{ $loop->index }}][day_fm]">
                                    </div>

                                </div>
                            </div>

                            <hr class="mx-3">

                            <div class="mb-2">
                                <h3 class="mt-2">Weitere Arbeiten</h3>

                                <div class="row mb-2">
                                    <!-- Arbeitsart -->
                                    <div id="work-type-entries-{{ $loop->index }}">
                                        @for ($entryIndex = 0; $entryIndex < $workTypeCount; $entryIndex++)
                                            <div id="work-type-entry-{{ $loop->index }}-{{ $entryIndex }}"
                                                class="work-type-entry-container @if ($entryIndex > 0) d-none @endif"
                                                data-project-index="{{ $loop->index }}"
                                                data-entry-index="{{ $entryIndex }}">
                                                <x-work-type-entry :project-index="$loop->index" :entry-index="$entryIndex"
                                                    :work-types="$workTypes" />
                                            </div>
                                        @endfor
                                    </div>
                                    <!-- Button -->
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
        function getProjectLabel(projectIndex) {
            const headerButton = document.querySelector(`button[data-bs-target="#collapse${projectIndex}"]`);
            return headerButton ? headerButton.textContent.trim() : `Projekt ${projectIndex}`;
        }

        function getVisibleWorkTypeSelects(projectIndex) {
            return Array.from(document.querySelectorAll(`.work-type-select[data-project-index="${projectIndex}"]`))
                .filter(select => {
                    const container = select.closest('.work-type-entry-container');
                    return container && !container.classList.contains('d-none');
                });
        }

        function calculateDiff(projectIndex) {
            const startInput = document.getElementById(`bs_start-${projectIndex}`);
            const endInput = document.getElementById(`bs_end-${projectIndex}`);
            const diffInput = document.getElementById(`bs_diff-${projectIndex}`);


            if (!startInput || !endInput) {
                return;
            }

            const start = startInput.value;
            const end = endInput.value;

            if (!start || !end) {
                diffInput.value = "";
                return;
            }

            let diff = parseFloat(end) - parseFloat(start);

            diffInput.value = diff.toFixed(2);
        }

        function syncWorkTypeOptions(projectIndex) {
            const visibleSelects = getVisibleWorkTypeSelects(projectIndex)
                .sort((a, b) => Number(a.dataset.entryIndex) - Number(b.dataset.entryIndex));

            // Keep the first occurrence of a selected work type and auto-correct duplicates in later entries.
            const usedValues = new Set();
            visibleSelects.forEach(select => {
                const currentValue = select.value;
                const hasDuplicate = currentValue !== "" && usedValues.has(currentValue);

                if (hasDuplicate || currentValue === "") {
                    const replacement = Array.from(select.options).find(option => !usedValues.has(option.value));
                    if (replacement) {
                        select.value = replacement.value;
                    }
                }

                if (select.value !== "") {
                    usedValues.add(select.value);
                }
            });

            visibleSelects.forEach(select => {
                const ownValue = select.value;
                const usedByOthers = new Set(
                    visibleSelects
                    .filter(otherSelect => otherSelect !== select)
                    .map(otherSelect => otherSelect.value)
                    .filter(value => value !== "")
                );

                Array.from(select.options).forEach(option => {
                    const isSelectedInAnotherEntry = usedByOthers.has(option.value);
                    option.disabled = isSelectedInAnotherEntry;
                    option.hidden = isSelectedInAnotherEntry;
                });

                if (ownValue === "" || usedByOthers.has(ownValue)) {
                    const firstAvailable = Array.from(select.options).find(option => !option.disabled);
                    if (firstAvailable) {
                        select.value = firstAvailable.value;
                    }
                }
            });

            const addButton = document.getElementById(`add-work-type-button-${projectIndex}`);
            if (addButton) {
                const hiddenEntryExists = Array.from(document.querySelectorAll(
                    `.work-type-entry-container[data-project-index="${projectIndex}"]`
                )).some(container => container.classList.contains('d-none'));

                addButton.disabled = !hiddenEntryExists;
                addButton.classList.toggle('disabled', !hiddenEntryExists);
            }
        }

        function showWorkTypeEntry(projectIndex, button) {
            const nextHiddenEntry = Array.from(document.querySelectorAll(
                `.work-type-entry-container[data-project-index="${projectIndex}"]`
            )).find(container => container.classList.contains('d-none'));

            if (!nextHiddenEntry) {
                return;
            }

            nextHiddenEntry.classList.remove('d-none');
            nextHiddenEntry.querySelectorAll('input, select, textarea').forEach(field => {
                field.disabled = false;
            });
            syncWorkTypeOptions(projectIndex);

            if (button) {
                const hasMoreHiddenEntries = Array.from(document.querySelectorAll(
                    `.work-type-entry-container[data-project-index="${projectIndex}"]`
                )).some(container => container.classList.contains('d-none'));
                button.disabled = !hasMoreHiddenEntries;
                button.classList.toggle('disabled', !hasMoreHiddenEntries);
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            const handledProjectIndexes = new Set();
            const form = document.getElementById('harvester-log-form');

            document.querySelectorAll('.work-type-entry-container.d-none').forEach(container => {
                container.querySelectorAll('input, select, textarea').forEach(field => {
                    field.disabled = true;
                });
            });

            document.querySelectorAll('[id^="bs_start-"]').forEach(startInput => {
                const projectIndex = startInput.id.substring("bs_start-".length);

                if (handledProjectIndexes.has(projectIndex)) {
                    return;
                }

                handledProjectIndexes.add(projectIndex);

                ["bs_start", "bs_end"].forEach(field => {
                    const input = document.getElementById(`${field}-${projectIndex}`);
                    if (input) {
                        input.addEventListener("input", () => calculateDiff(projectIndex));
                    }
                });
            });

            document.querySelectorAll('[id^="start-"]').forEach(startInput => {
                const projectIndex = startInput.id.substring("start-".length);

                if (handledProjectIndexes.has(projectIndex)) {
                    return;
                }

                handledProjectIndexes.add(projectIndex);

            });

            document.querySelectorAll('.work-type-select').forEach(select => {
                select.addEventListener("change", () => {
                    syncWorkTypeOptions(select.dataset.projectIndex);
                });
            });

            handledProjectIndexes.forEach(projectIndex => {
                syncWorkTypeOptions(projectIndex);
            });


        });
    </script>
</body>

</html>
