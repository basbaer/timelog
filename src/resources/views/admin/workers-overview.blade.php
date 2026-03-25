<!doctype html>
<html lang="de">

@include('partials.head', ['title' => 'Mitarbeiterübersicht'])

<body>
    @include('partials.admin_navbar', ['active' => 'workers'])

    <div class="container mt-3">
        <div class="list-group">
           @foreach ($workers as $worker)
               <a href="{{ route('admin.worker.show', $worker->id) }}" class="list-group-item list-group-item-action">{{ $worker->first_name . " " . $worker->last_name }}</a>
           @endforeach
            
        </div>
    </div>

    <div class="container mt-3 d-flex justify-content-center">
        <a href="{{ route('admin.workers.add') }}" type="button" class="btn btn-primary">Arbeiter hinzufügen</a>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

</body>

</html>