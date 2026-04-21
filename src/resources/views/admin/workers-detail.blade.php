<!doctype html>
<html lang="de">

@include('partials.head', ['title' => 'Dashboard'])

<body>
    @include('partials.admin_navbar', ['active' => 'workers'])

    <div class="mt-3">
        <div class="row">
            <div class="col justify-content-start d-flex">
                <h2 class="h2 m-3">{{ $name }}</h2>
            </div>
            <div class="col justify-content-end d-flex">
                <a href="{{ route('admin.worker.card', ['id' => $id]) }}" type="button"
                    class="btn btn-secondary m-3">Kontaktinformationen</a>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="container mt-3">
            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        </div>
        
    @endif

    <div class=" m-3">
        <table class="table">
            <thead>
                <tr>
                    <th scope="col">Tag</th>
                    <th scope="col">Datum</th>
                    <th scope="col">Von</th>
                    <th scope="col">Bis</th>
                    <th scope="col">Pause</th>
                    <th scope="col">Gesamt</th>
                    <th scope="col">Baustelle</th>
                    <th scope="col">Arbeitsart</th>
                    <th scope="col">Stunden</th>
                    <th scope="col">Anmerkung</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($log_entries as $row)
                    <tr>
                        <th scope="row">{{ $row->weekday }}</th>
                        <td>{{ $row->date }}</td>
                        <td>{{ $row->start}}</td>
                        <td>{{ $row->end }}</td>
                        <td>{{ $row->pause}}</td>
                        <td>{{ $row->total }}</td>
                        <td>{{ $row->project_client }} ({{ $row->project_location }})</td>
                        <td>{{ $row->working_type_name }}</td>
                        <td>{{ $row->hours }}</td>
                        <td>{{ $row->comment }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="container mt-3 d-flex justify-content-center">
        <a href="{{ route('admin.worker.log.create', ['id' => $id]) }}" role="button" class="btn btn-primary">Eintrag hinzufügen</a>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>

</body>

</html>
