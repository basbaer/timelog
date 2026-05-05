<!doctype html>
<html lang="de">

@include('partials.head', ['title' => 'Log'])

<body>
    <div class="container d-flex justify-content-between align-items-center">
        <h1 class="mt-2">{{ $name }}</h1>
        <!-- Logout Button -->
        <div class="d-flex justify-content-end">
            <a href="/logout" class="btn btn-outline-danger">Logout</a>
        </div>
    </div>
    <div id="form-errors" class="container alert alert-danger @if (!$errors->any()) d-none @endif">
        <ul class="mb-0" id="form-errors-list">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    <form id="forstwirt-log-form" class="container" method="POST" action="{{ route('log.forstwirt.store') }}">
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
        <div class="container my-3 px-0">
            <label for="date" class="form-label">{{ __('form.date') }}</label>
            <input id="date" name="log_date" class="form-control" type="date" lang="de"
                @unless ($isAdmin) readonly @endunless />
            <script>
                document.addEventListener("DOMContentLoaded", function() {
                    const input = document.getElementById("date");

                    const now = new Date();
                    const yyyy = now.getFullYear();
                    const mm = String(now.getMonth() + 1).padStart(2, "0");
                    const dd = String(now.getDate()).padStart(2, "0");
                    const today = `${yyyy}-${mm}-${dd}`; // YYYY-MM-DD

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
                    <div id="collapse{{ $loop->index }}" class="accordion-collapse collapse"
                        data-bs-parent="#accordionProjects">
                        <div class="accordion-body px-2">
                            <input type="hidden" name="work_logs[{{ $loop->index }}][project_id]"
                                value="{{ $project->id }}">

                            <!-- Arbeitszeit -->
                            <label for="worktype-dropdown-{{ $loop->index }}" class="form-label">
                                <strong>{{ __('form.working_time') }}</strong>
                            </label>

                            <div class="row d-flex mb-2">
                                <!-- Von -->
                                <div class="col-sm-auto col-6 mb-2">
                                    <label for="start-{{ $loop->index }}"
                                        class="form-label">{{ __('form.from') }}</label>
                                    <input type="time" id="start-{{ $loop->index }}" class="form-control"
                                        name="work_logs[{{ $loop->index }}][start]" lang="de-DE" step="900">
                                </div>
                                <!-- Bis -->
                                <div class="col-sm-auto col-6 mb-2">
                                    <label for="end-{{ $loop->index }}"
                                        class="form-label">{{ __('form.to') }}</label>
                                    <input type="time" id="end-{{ $loop->index }}" class="form-control"
                                        name="work_logs[{{ $loop->index }}][end]" lang="de-DE" step="900">
                                </div>

                                <div class="d-sm-none w-100"></div>

                                <div class="col-sm-3 col-6">
                                    <label for="pause-{{ $loop->index }}"
                                        class="form-label">{{ __('form.pause') }}</label>
                                    <input type="number" id="pause-{{ $loop->index }}" class="form-control"
                                        name="work_logs[{{ $loop->index }}][pause]" min="0" value="0" step="15">
                                </div>
                                <div class="col-3">
                                    <label for="summe-{{ $loop->index }}"
                                        class="form-label">{{ __('form.sum') }}</label>
                                    <input type="text" id="summe-{{ $loop->index }}" class="form-control"
                                        name="work_logs[{{ $loop->index }}][sum]" readonly>
                                </div>
                            </div>
                            <hr class="mx-3">

                            <!-- Arbeitsart -->
                            <div id="work-type-entries-{{ $loop->index }}">
                                @for ($entryIndex = 0; $entryIndex < $workTypeCount; $entryIndex++)
                                    <div id="work-type-entry-{{ $loop->index }}-{{ $entryIndex }}"
                                        class="work-type-entry-container @if ($entryIndex > 0) d-none @endif"
                                        data-project-index="{{ $loop->index }}" data-entry-index="{{ $entryIndex }}">
                                        <x-work-type-entry :project-index="$loop->index" :entry-index="$entryIndex" :work-types="$workTypes" />
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
        function parseHHMMToMinutes(value) {
            if (!value || !value.includes(":")) {
                return null;
            }

            const [hoursStr, minutesStr] = value.split(":");
            const hours = parseInt(hoursStr, 10);
            const minutes = parseInt(minutesStr, 10);

            if (Number.isNaN(hours) || Number.isNaN(minutes)) {
                return null;
            }

            return (hours * 60) + minutes;
        }

        function formatMinutesToHHMM(minutes) {
            const safeMinutes = Math.max(0, Math.round(minutes));
            const hours = Math.floor(safeMinutes / 60);
            const remainingMinutes = safeMinutes % 60;

            return `${String(hours).padStart(2, "0")}:${String(remainingMinutes).padStart(2, "0")}`;
        }

        function getProjectLabel(projectIndex) {
            const headerButton = document.querySelector(`button[data-bs-target="#collapse${projectIndex}"]`);
            return headerButton ? headerButton.textContent.trim() : `Projekt ${projectIndex}`;
        }

        function validateEntryHoursAgainstWorkingTime(projectIndex) {
            const sumInput = document.getElementById(`summe-${projectIndex}`);
            if (!sumInput || !sumInput.value) {
                return {
                    valid: true,
                    message: null,
                };
            }

            const workingMinutes = parseHHMMToMinutes(sumInput.value);
            if (workingMinutes === null) {
                return {
                    valid: true,
                    message: null,
                };
            }

            const hourInputs = Array.from(document.querySelectorAll(
                `.work-type-entry-container[data-project-index="${projectIndex}"]:not(.d-none) input[id$="-hours"]`
            )).filter(input => !input.disabled);

            const entryMinutes = hourInputs.reduce((total, input) => {
                const value = parseHHMMToMinutes(input.value);
                if (value === null) {
                    return total;
                }

                return total + value;
            }, 0);

            if (entryMinutes !== workingMinutes) {
                const projectLabel = getProjectLabel(projectIndex);
                const entryHours = formatMinutesToHHMM(entryMinutes);
                const workingHours = formatMinutesToHHMM(workingMinutes);

                return {
                    valid: false,
                    message: `{{ __('form.error_working_hours') }} ${projectLabel}: {{ __('form.sum_hours') }} (${entryHours}) vs. {{ __('form.total_hours') }} (${workingHours})`
                };
            }

            return {
                valid: true,
                message: null,
            };
        }

        function validateFormHoursConsistency() {
            const projectIndexes = Array.from(document.querySelectorAll('[id^="start-"]'))
                .map(startInput => startInput.id.substring("start-".length));

            const messages = projectIndexes
                .map(validateEntryHoursAgainstWorkingTime)
                .filter(result => !result.valid)
                .map(result => result.message);

            const errorBox = document.getElementById('form-errors');
            const errorList = document.getElementById('form-errors-list');

            if (errorBox && errorList) {
                errorList.innerHTML = '';

                messages.forEach(message => {
                    const item = document.createElement('li');
                    item.textContent = message;
                    errorList.appendChild(item);
                });

                errorBox.classList.toggle('d-none', messages.length === 0);
            }

            if (messages.length > 0) {
                return false;
            }

            return true;
        }

        function calculateWorkingHours(projectIndex) {
            const startInput = document.getElementById(`start-${projectIndex}`);
            const endInput = document.getElementById(`end-${projectIndex}`);
            const pauseInput = document.getElementById(`pause-${projectIndex}`);
            const sumInput = document.getElementById(`summe-${projectIndex}`);

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

            let diff = (endDate - startDate) / 60000; // minutes

            // Handle end after midnight if needed:
            if (diff < 0) diff += 24 * 60;

            diff -= pause; // subtract pause
            if (diff < 0) diff = 0;

            const hours = Math.floor(diff / 60);
            const minutes = diff % 60;

            sumInput.value = formatMinutesToHHMM((hours * 60) + minutes);
        }

        function getVisibleWorkTypeSelects(projectIndex) {
            return Array.from(document.querySelectorAll(`.work-type-select[data-project-index="${projectIndex}"]`))
                .filter(select => {
                    const container = select.closest('.work-type-entry-container');
                    return container && !container.classList.contains('d-none');
                });
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
            const form = document.getElementById('forstwirt-log-form');

            document.querySelectorAll('.work-type-entry-container.d-none').forEach(container => {
                container.querySelectorAll('input, select, textarea').forEach(field => {
                    field.disabled = true;
                });
            });

            document.querySelectorAll('[id^="start-"]').forEach(startInput => {
                const projectIndex = startInput.id.substring("start-".length);

                if (handledProjectIndexes.has(projectIndex)) {
                    return;
                }

                handledProjectIndexes.add(projectIndex);

                ["start", "end", "pause"].forEach(field => {
                    const input = document.getElementById(`${field}-${projectIndex}`);
                    if (input) {
                        input.addEventListener("input", () => calculateWorkingHours(projectIndex));
                    }
                });
            });

            document.querySelectorAll('.work-type-select').forEach(select => {
                select.addEventListener("change", () => {
                    syncWorkTypeOptions(select.dataset.projectIndex);
                });
            });

            handledProjectIndexes.forEach(projectIndex => {
                syncWorkTypeOptions(projectIndex);
            });

            if (form) {
                form.addEventListener('submit', function(event) {
                    if (!validateFormHoursConsistency()) {
                        event.preventDefault();
                    }
                });
            }
        });
    </script>
</body>

</html>
