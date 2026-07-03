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
        <form action="{{ route('admin.projects.store') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="location" class="form-label">Ort</label>
                <input type="text" class="form-control @error('location') is-invalid @enderror" id="location"
                    name="location" value="{{ old('location') }}" placeholder="Geben Sie den Ort ein">
                @error('location')
                    <div class="invalid-feedback">Ort ist erforderlich</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="date" class="form-label">Startdatum</label>
                <input type="date" class="form-control @error('date') is-invalid @enderror" id="date"
                    name="date" value="{{ old('date') }}" placeholder="Geben Sie das Datum ein">
                @error('date')
                    <div class="invalid-feedback">Datum ist erforderlich</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="client" class="form-label">Förster/Waldbesitzer</label>
                <input type="text" class="form-control @error('client') is-invalid @enderror" id="client"
                    name="client" value="{{ old('client') }}" placeholder="Geben Sie den Auftraggeber ein">
                @error('client')
                    <div class="invalid-feedback">Auftraggeber ist erforderlich</div>
                @enderror
            </div>
            <div class="mb-3">
                <label for="workerSelection" class="form-label">Mitarbeiter auswählen</label>
                <ul class="list-group" id="workerSelection">
                    @foreach ($roles as $role)
                        <li class="list-group-item">
                            <input class="form-check-input me-1" type="checkbox" name="roles[]"
                                value="{{ $role->id }}" id="roleCheckbox{{ $role->id }}"
                                @checked(in_array($role->id, old('roles', [])))>
                            <label class="form-check-label"
                                for="roleCheckbox{{ $role->id }}">{{ $role->name }}</label>
                        </li>
                    @endforeach
                </ul>
                @error('roles')
                    <div class="text-danger mt-2"> Mindestens eine Arbeitergruppe muss ausgewählt werden </div>
                @enderror
            </div>
            <div class="mb-3 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Projekt erstellen</button>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>

</body>

</html>
