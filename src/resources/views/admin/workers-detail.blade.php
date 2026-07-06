<!doctype html>
<html lang="de">

@include('partials.head', ['title' => 'Dashboard', 'withBootstrapIcons' => true])

<body>
    @include('partials.admin_navbar', ['active' => 'workers'])

    @include('partials.admin-worker-detail-top', [
        'name' => $name,
        'worker_id' => $worker_id,
        'month' => $month,
        'previousMonth' => $previousMonth,
        'nextMonth' => $nextMonth,
    ])

    @if ($role === 'forstwirt')
        <div class="m-3">
            <x-worker-detail-forstwirt-table :log_entries="$log_entries" :worker_id="$worker_id" />
        </div>
    @elseif ($role === 'harvester')
        <div class="m-3">
            <x-worker-detail-harvester-table :log_entries="$log_entries" :worker_id="$worker_id" />
        </div>
    @elseif ($role === 'rueckezug')
        <div class="m-3">
            <x-worker-detail-rueckezug-table :log_entries="$log_entries" :worker_id="$worker_id" />
        </div>
    @endif
    <div class="container-fluid px-3">
        <div class="position-relative d-flex align-items-center w-100">
            <a href="{{ route('admin.worker.log.create', ['worker_id' => $worker_id]) }}" role="button"
                class="btn btn-primary position-absolute start-50 translate-middle-x">Eintrag hinzufügen</a>
            <a href="{{ route('admin.workers.overview') }}" role="button" class="btn btn-secondary ms-auto">
                Zurück zur Übersicht</a>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>

</body>

</html>
