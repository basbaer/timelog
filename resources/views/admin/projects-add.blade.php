<!doctype html>
<html lang="de">

@include('partials.head', ['title' => 'Dashboard'])

<body>
    @include('partials.admin_navbar', ['active' => 'projects'])

    <div class="container my-4">
        @if ($project)
            <h2> {{ $project->location . ' | ' . $project->date->format('m/Y') . ' | ' . $project->client }} </h2>
        @else
            <h2>Neues Projekt anlegen</h2>
        @endif
        <form action="{{ $project ? route('admin.projects.update', $project->id) : route('admin.projects.store') }}"
            method="POST">
            @csrf
            @if ($project)
                @method('PUT')
            @endif
            <div class="mb-3">
                <label for="location" class="form-label">Ort</label>
                <input type="text" class="form-control @error('location') is-invalid @enderror" id="location"
                    name="location" value="{{ old('location', $project ? $project->location : null) }}"
                    placeholder="Geben Sie den Ort ein">
                @error('location')
                    <div class="invalid-feedback">Ort ist erforderlich</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="date" class="form-label">Startdatum</label>
                <input type="date" class="form-control @error('date') is-invalid @enderror" id="date"
                    name="date" value="{{ old('date', $project ? $project->date->format('Y-m-d') : null) }}"
                    placeholder="Geben Sie das Datum ein">
                @error('date')
                    <div class="invalid-feedback">Datum ist erforderlich</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="client" class="form-label">Förster/Waldbesitzer</label>
                <input type="text" class="form-control @error('client') is-invalid @enderror" id="client"
                    name="client" value="{{ old('client', $project ? $project->client : null) }}"
                    placeholder="Geben Sie den Auftraggeber ein">
                @error('client')
                    <div class="invalid-feedback">Auftraggeber ist erforderlich</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="workerSelection" class="form-label">Mitarbeiter auswählen</label>
                @php
                    $selectedRoleIds = collect(old('roles', $assignedRoleIds ?? []))
                        ->map(fn($id) => (int) $id)
                        ->all();
                    $selectedWorkerIds = collect(old('workers', $assignedUserIds ?? []))
                        ->map(fn($id) => (int) $id)
                        ->all();
                @endphp
                <ul class="list-group" id="workerSelection">
                    @foreach ($roles as $role)
                        <li class="list-group-item">
                            <input class="form-check-input me-1 role-checkbox" type="checkbox" name="roles[]"
                                value="{{ $role->id }}" id="roleCheckbox{{ $role->id }}"
                                data-role-id="{{ $role->id }}" @checked(in_array((int) $role->id, $selectedRoleIds, true))>
                            <label class="form-check-label"
                                for="roleCheckbox{{ $role->id }}">{{ $role->name }}</label>

                            <ul class="list-group mt-2 ms-4">
                                @forelse ($role->users as $worker)
                                    @php
                                        $workerDisplayName = trim(
                                            ($worker->first_name ?? '') . ' ' . ($worker->last_name ?? ''),
                                        );
                                    @endphp
                                    <li class="list-group-item border-0 py-1 px-0">
                                        <input class="form-check-input me-1 worker-checkbox" type="checkbox"
                                            name="workers[]" value="{{ $worker->id }}"
                                            id="workerCheckbox{{ $worker->id }}" data-role-id="{{ $role->id }}"
                                            @checked(in_array((int) $worker->id, $selectedWorkerIds, true))>
                                        <label class="form-check-label" for="workerCheckbox{{ $worker->id }}">
                                            {{ $workerDisplayName !== '' ? $workerDisplayName : 'Mitarbeiter #' . $worker->id }}
                                        </label>
                                    </li>
                                @empty
                                    <li class="list-group-item border-0 py-1 px-0 text-muted">
                                        Keine Mitarbeiter für diese Rolle vorhanden.
                                    </li>
                                @endforelse
                            </ul>
                        </li>
                    @endforeach
                </ul>
                @error('roles')
                    <div class="text-danger mt-2"> Mindestens eine Arbeitergruppe muss ausgewählt werden </div>
                @enderror
            </div>
            <div class="mb-3 d-flex justify-content-end">
                <a href="{{ $project ? route('admin.project.detail', ['id' => $project->id]) : route('admin.projects.overview') }}"
                    class="btn btn-secondary me-2">Abbrechen</a>
                @if ($project)
                    <button type="submit" class="btn btn-primary">Projekt aktualisieren</button>
                @else
                    <button type="submit" class="btn btn-primary">Projekt erstellen</button>
                @endif
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleCheckboxes = Array.from(document.querySelectorAll('.role-checkbox'));

            function getWorkersForRole(roleId) {
                return Array.from(document.querySelectorAll('.worker-checkbox[data-role-id="' + roleId + '"]'));
            }

            function refreshRoleState(roleCheckbox) {
                const roleId = roleCheckbox.dataset.roleId;
                const workers = getWorkersForRole(roleId);

                if (workers.length === 0) {
                    roleCheckbox.indeterminate = false;
                    return;
                }

                const checkedCount = workers.filter(worker => worker.checked).length;

                if (checkedCount === 0) {
                    roleCheckbox.checked = false;
                    roleCheckbox.indeterminate = false;
                    return;
                }

                if (checkedCount === workers.length) {
                    roleCheckbox.checked = true;
                    roleCheckbox.indeterminate = false;
                    return;
                }

                roleCheckbox.checked = true;
                roleCheckbox.indeterminate = true;
            }

            roleCheckboxes.forEach(roleCheckbox => {
                const roleId = roleCheckbox.dataset.roleId;
                const workers = getWorkersForRole(roleId);

                roleCheckbox.addEventListener('change', function() {
                    workers.forEach(worker => {
                        worker.checked = roleCheckbox.checked;
                    });

                    roleCheckbox.indeterminate = false;
                });

                workers.forEach(worker => {
                    worker.addEventListener('change', function() {
                        refreshRoleState(roleCheckbox);
                    });
                });
            });

            roleCheckboxes.forEach(roleCheckbox => {
                const roleId = roleCheckbox.dataset.roleId;
                const workers = getWorkersForRole(roleId);

                const hasCheckedWorkers = workers.some(worker => worker.checked);

                if (roleCheckbox.checked && workers.length > 0 && !hasCheckedWorkers) {
                    workers.forEach(worker => {
                        worker.checked = true;
                    });
                }

                refreshRoleState(roleCheckbox);
            });
        });
    </script>

</body>

</html>
