<!doctype html>
<html lang="de">

@include('partials.head', ['title' => 'Eintrag bearbeiten'])

<body>
    @include('partials.log_header', ['name' => $name, 'worker_id' => $user_id])
    @include('partials.log_form_errors', ['errors' => $errors])

    <form class="container" method="POST" action="{{ route('log.forstwirt.update', ['user_id' => $user_id, 'log_id' => $log->id]) }}">
        @csrf
        @method('PUT')

        @php($dateValue = old('date', \Carbon\Carbon::parse($log->date)->format('Y-m-d')))
        @php($projectValue = old('project_id', $log->project_id))
        @php($workingTypeValue = old('working_type_id', $log->working_type_id))

        <div class="container my-3 px-0">
            <label for="date" class="form-label">Datum</label>
            <input id="date" name="date" class="form-control" type="date" value="{{ $dateValue }}" readonly />
        </div>

        <div class="container my-3 px-0">
            <label for="project_id" class="form-label">Baustelle</label>
            <select id="project_id" name="project_id" class="form-select">
                @foreach ($projects as $project)
                    <option value="{{ $project->id }}" @selected((int) $projectValue === (int) $project->id)>
                        {{ $project->title }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="container my-3 px-0">
            <label for="working_type_id" class="form-label">Arbeitsart</label>
            <select id="working_type_id" name="working_type_id" class="form-select">
                @foreach ($workTypes as $workType)
                    <option value="{{ $workType->id }}" @selected((int) $workingTypeValue === (int) $workType->id)>
                        {{ $workType->name ?? $workType->slug }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="row g-3">
            <div class="col-sm-6 col-lg-3">
                <label for="start" class="form-label">Von</label>
                <input id="start" name="start" class="form-control" type="time" step="900"
                    value="{{ old('start', $log->start ? \Carbon\Carbon::parse($log->start)->format('H:i') : '') }}">
            </div>

            <div class="col-sm-6 col-lg-3">
                <label for="end" class="form-label">Bis</label>
                <input id="end" name="end" class="form-control" type="time" step="900"
                    value="{{ old('end', $log->end ? \Carbon\Carbon::parse($log->end)->format('H:i') : '') }}">
            </div>

            <div class="col-sm-6 col-lg-3">
                <label for="pause" class="form-label">Pause (Minuten)</label>
                <input id="pause" name="pause" class="form-control" type="number" min="0" step="15"
                    value="{{ old('pause', $log->pause ?? 0) }}">
            </div>

            <div class="col-sm-6 col-lg-3">
                <label for="sum" class="form-label">Gesamt</label>
                <input id="sum" name="sum" class="form-control" type="time" step="900"
                    value="{{ old('sum', $log->sum ? \Carbon\Carbon::parse($log->sum)->format('H:i') : '') }}">
            </div>
        </div>

        <div class="mb-3 mt-3">
            <label for="comment" class="form-label">Anmerkung</label>
            <textarea id="comment" name="comment" class="form-control" rows="4">{{ old('comment', $log->comment) }}</textarea>
        </div>

        <div class="container d-flex justify-content-center">
            <button class="btn btn-success my-4" type="submit">Eintrag aktualisieren</button>
        </div>
    </form>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>
</body>

</html>
