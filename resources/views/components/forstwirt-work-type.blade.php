@props([
    'projectIndex',
    'projectId',
    'entryIndex',
    'workTypes' => [],
    'hidden' => false,
])

@php
    $entryBase = "work-{$projectIndex}-{$entryIndex}";
    $entryClass = 'work-type-entry-container';
    $oldEntry = old("work_logs.$projectId.$entryIndex", []);
    $hasOldEntry = is_array($oldEntry)
        && collect($oldEntry)->contains(fn ($value) => trim((string) $value) !== '');

    if ($hidden && ! $hasOldEntry) {
        $entryClass .= ' d-none';
    }
@endphp

<div class="{{ $entryClass }}" data-project-index="{{ $projectIndex }}" data-entry-index="{{ $entryIndex }}">
    <div class="row mb-2">
        <div class="col-md-4 mb-2">
            <label for="{{ $entryBase }}-type" class="form-label">
                <strong>{{ __('form.working_type') }}</strong>
            </label>
            <select id="{{ $entryBase }}-type" class="form-select work-type-select"
                name="work_logs[{{ $projectId }}][{{ $entryIndex }}][type]" data-project-index="{{ $projectIndex }}"
                data-entry-index="{{ $entryIndex }}">
                @php
                    $selectedWorkType = old("work_logs.$projectId.$entryIndex.type", array_key_first($workTypes));
                @endphp
                @foreach ($workTypes as $type => $label)
                    <option value="{{ $type }}" @selected($selectedWorkType === $type)>{{ $label }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-6 col-md-2 mb-2">
            <label for="{{ $entryBase }}-start" class="form-label">{{ __('form.from') }}</label>
            <input type="time" id="{{ $entryBase }}-start" class="form-control"
                name="work_logs[{{ $projectId }}][{{ $entryIndex }}][start]" lang="de-DE" step="900"
                value="{{ old("work_logs.$projectId.$entryIndex.start") }}">
        </div>

        <div class="col-6 col-md-2 mb-2">
            <label for="{{ $entryBase }}-end" class="form-label">{{ __('form.to') }}</label>
            <input type="time" id="{{ $entryBase }}-end" class="form-control"
                name="work_logs[{{ $projectId }}][{{ $entryIndex }}][end]" lang="de-DE" step="900"
                value="{{ old("work_logs.$projectId.$entryIndex.end") }}">
        </div>

        <div class="col-6 col-md-2 mb-2">
            <label for="{{ $entryBase }}-pause" class="form-label">{{ __('form.pause') }}</label>
            <input type="number" id="{{ $entryBase }}-pause" class="form-control"
                name="work_logs[{{ $projectId }}][{{ $entryIndex }}][pause]" min="0"
                value="{{ old("work_logs.$projectId.$entryIndex.pause", 0) }}" step="15">
        </div>

        <div class="col-6 col-md-2 mb-2">
            <label for="{{ $entryBase }}-sum" class="form-label">{{ __('form.working_time') }}</label>
            <input type="text" id="{{ $entryBase }}-sum" class="form-control"
                name="work_logs[{{ $projectId }}][{{ $entryIndex }}][sum]" readonly
                value="{{ old("work_logs.$projectId.$entryIndex.sum") }}">
        </div>
    </div>

    <div class="mb-3">
        <label for="{{ $entryBase }}-comment" class="form-label">{{ __('form.comment') }}</label>
        <textarea class="form-control" id="{{ $entryBase }}-comment"
            name="work_logs[{{ $projectId }}][{{ $entryIndex }}][comment]" rows="3">{{ old("work_logs.$projectId.$entryIndex.comment") }}</textarea>
    </div>
</div>

@once
    <script>
        // Konvertiert eine Anzahl Minuten in einen HH:MM-String (z.B. 90 -> "01:30").
        // Nimmt negative Werte nicht an und rundet Eingaben auf ganze Minuten.
        function formatMinutesToHHMM(minutes) {
            const safeMinutes = Math.max(0, Math.round(minutes));
            const hours = Math.floor(safeMinutes / 60);
            const remainingMinutes = safeMinutes % 60;

            return `${String(hours).padStart(2, "0")}:${String(remainingMinutes).padStart(2, "0")}`;
        }

        // Liefert das DOM-Element für einen Eintrag anhand von projectIndex und entryIndex.
        // Gibt `null` zurück, wenn kein Element gefunden wurde.
        function getEntryContainer(projectIndex, entryIndex) {
            return document.querySelector(
                `.work-type-entry-container[data-project-index="${projectIndex}"][data-entry-index="${entryIndex}"]`
            );
        }

        // Berechnet die Arbeitszeit (in Minuten) für einen Eintrag und schreibt das
        // formatierte Ergebnis in das `sum`-Feld. Berücksichtigt Start, Ende und Pause
        // sowie Überläufe über Mitternacht.
        function calculateWorkingHours(projectIndex, entryIndex) {
            const container = getEntryContainer(projectIndex, entryIndex);

            if (!container) {
                return;
            }

            const startInput = container.querySelector('input[name$="[start]"]');
            const endInput = container.querySelector('input[name$="[end]"]');
            const pauseInput = container.querySelector('input[name$="[pause]"]');
            const sumInput = container.querySelector('input[name$="[sum]"]');

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

            // Falls Endzeit vor Startzeit liegt, wurde über Mitternacht gearbeitet.
            if (diff < 0) {
                diff += 24 * 60;
            }

            diff -= pause;
            if (diff < 0) {
                diff = 0;
            }

            sumInput.value = formatMinutesToHHMM(diff);
        }

        // Liefert alle sichtbaren (nicht ausgeblendeten) Work-Type-Select-Elemente
        // für ein bestimmtes Projekt zurück. Diese werden zur Vermeidung von Duplikaten
        // in den Selekt-Optionen verwendet.
        function getVisibleWorkTypeSelects(projectIndex) {
            return Array.from(document.querySelectorAll(`.work-type-select[data-project-index="${projectIndex}"]`))
                .filter(select => {
                    const container = select.closest('.work-type-entry-container');
                    return container && !container.classList.contains('d-none');
                });
        }

        // Synchronisiert die Options/Selections aller sichtbaren Work-Type-Selects so,
        // dass jede Arbeitsart nur einmal verwendet wird. Deaktiviert/verbirgt Optionen,
        // die bereits in anderen Einträgen gewählt wurden, und wählt ggf. eine Ersatzoption.
        function syncWorkTypeOptions(projectIndex) {
            const visibleSelects = getVisibleWorkTypeSelects(projectIndex)
                .sort((a, b) => Number(a.dataset.entryIndex) - Number(b.dataset.entryIndex));

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

            // Schalte den "Hinzufügen"-Button ab, wenn keine versteckten Einträge mehr vorhanden sind.
            const addButton = document.getElementById(`add-work-type-button-${projectIndex}`);
            if (addButton) {
                const hiddenEntryExists = Array.from(document.querySelectorAll(
                    `.work-type-entry-container[data-project-index="${projectIndex}"]`
                )).some(container => container.classList.contains('d-none'));

                addButton.disabled = !hiddenEntryExists;
                addButton.classList.toggle('disabled', !hiddenEntryExists);
            }
        }

        // Zeigt den nächsten versteckten Eintrag an und synchronisiert danach die Optionen.
        // Falls ein Button übergeben wird, wird dessen Zustand entsprechend angepasst.
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

        // Initialisiert die Eintragsliste: deaktiviert versteckte Felder, hängt
        // Event-Listener an Start/End/Pause-Felder an und synchronisiert die Selects
        // für jedes Projekt einmal beim Laden der Seite.
        function initForstwirtWorkTypeEntries() {
            document.querySelectorAll('.work-type-entry-container.d-none').forEach(container => {
                container.querySelectorAll('input, select, textarea').forEach(field => {
                    field.disabled = true;
                });
            });

            document.querySelectorAll('.work-type-entry-container').forEach(container => {
                const projectIndex = container.dataset.projectIndex;
                const entryIndex = container.dataset.entryIndex;

                ["start", "end", "pause"].forEach(field => {
                    const input = container.querySelector(`input[name$="[${field}]"]`);
                    if (input) {
                        input.addEventListener("input", () => calculateWorkingHours(projectIndex, entryIndex));
                    }
                });
            });

            document.querySelectorAll('.work-type-select').forEach(select => {
                select.addEventListener("change", () => {
                    syncWorkTypeOptions(select.dataset.projectIndex);
                });
            });

            const handledProjectIndexes = new Set(
                Array.from(document.querySelectorAll('.work-type-entry-container')).map(container => container.dataset.projectIndex)
            );

            handledProjectIndexes.forEach(projectIndex => {
                syncWorkTypeOptions(projectIndex);
            });

            document.querySelectorAll('.work-type-entry-container').forEach(container => {
                calculateWorkingHours(container.dataset.projectIndex, container.dataset.entryIndex);
            });
        }

        window.showWorkTypeEntry = showWorkTypeEntry;
        window.initForstwirtWorkTypeEntries = initForstwirtWorkTypeEntries;
    </script>
@endonce