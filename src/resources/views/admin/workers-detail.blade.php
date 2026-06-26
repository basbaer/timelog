<!doctype html>
<html lang="de">

@include('partials.head', ['title' => 'Dashboard', 'withBootstrapIcons' => true])

<body>
    @include('partials.admin_navbar', ['active' => 'workers'])

    @include('partials.admin_worker_detail_top', [
        'name' => $name,
        'worker_id' => $worker_id,
        'month' => $month,
        'previousMonth' => $previousMonth,
        'nextMonth' => $nextMonth,
    ])

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
                    <th scope="col">Anmerkung</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($log_entries as $row)
                    <tr>
                        <th scope="row">
                            @if ($row->show_date)
                                {{ $row->weekday }}
                            @endif
                        </th>
                        <td>
                            @if ($row->show_date)
                                {{ $row->date }}
                            @endif
                        </td>
                        <td>{{ $row->start }}</td>
                        <td>{{ $row->end }}</td>
                        <td>{{ $row->pause }}</td>
                        <td>{{ $row->sum }}</td>
                        <td>{{ $row->project_client }} ({{ $row->project_location }})</td>
                        <td>{{ $row->working_type_name }}</td>
                        <td>{{ $row->comment }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="container my-3 d-flex justify-content-center">
        <a href="{{ route('admin.worker.log.create', ['worker_id' => $worker_id]) }}" role="button" class="btn btn-primary">Eintrag
            hinzufügen</a>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>

</body>

</html>
