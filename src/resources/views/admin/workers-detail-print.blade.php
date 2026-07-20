<!doctype html>
<html lang="de">

@include('partials.head', ['title' => 'Dashboard', 'withBootstrapIcons' => true])

<body>

    <div class="row m-3">
         {{ $selectedProject }}
    </div>


    @if ($role === 'forstwirt')
        <div class="m-3">
            <x-worker-detail-forstwirt-table :log_entries="$logEntries" :worker_id="$worker_id" />
        </div>
    @elseif ($role === 'harvester')
        <div class="m-3">
            <x-worker-detail-harvester-table :log_entries="$logEntries" :worker_id="$worker_id" />
        </div>
    @elseif ($role === 'rueckezug')
        <div class="m-3">
            <x-worker-detail-rueckezug-table :log_entries="$logEntries" :worker_id="$worker_id" />
        </div>
    @endif

    <div class="container-fluid px-3 mb-3 ms-2">
        <div class="position-relative d-flex row w-100">
            <a href="{{ route('admin.workers.overview') }}" role="button" class="btn btn-secondary col-auto me-auto">
                Zurück zur Übersicht</a>
            <a href="{{ route('workers.print', ['worker_id' => $worker_id, 'project' => $selectedProject]) }}" role="button" class="btn btn-success col-auto me-auto">
                Drucken</a>
            <a href="{{ route('admin.worker.log.create', ['worker_id' => $worker_id]) }}" role="button"
                class="btn btn-primary col-auto">Eintrag hinzufügen</a>
            
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>

</body>

</html>
