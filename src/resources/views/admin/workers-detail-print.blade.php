<!doctype html>
<html lang="de">

@include('partials.head', ['title' => 'Dashboard', 'withBootstrapIcons' => true])

<body>

    <div class="container-fluid d-flex align-items-center justify-content-center gap-5 px-3 mb-3 ms-2">
        @if ($project !== 'all')
            <div>
                Projekt: {{ $project->title }}
            </div>
        @endif
        <div>
            Arbeiter: {{ $name }}
        </div>
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

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>

</body>

</html>
