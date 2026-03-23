<!doctype html>
<html lang="de">

@include('partials.head', ['title' => 'Mitarbeiterübersicht'])

<body>
    <nav class="navbar navbar-expand bg-body-tertiary">
        <div class="container-fluid">

            <div class="collapse navbar-collapse justify-content-center" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link " href="#">Projekte</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Mitarbeiter</a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-3">
        <div class="list-group">
           @foreach ($workers as $worker)
               <a href="{{ route('worker.show', $worker->id) }}" class="list-group-item list-group-item-action">{{ $worker->first_name . " " . $worker->last_name }}</a>
           @endforeach
            
        </div>
    </div>

    <div class="container mt-3 d-flex justify-content-end ">
        <a href="dash-addWorker.html" type="button" class="btn btn-primary">Arbeiter hinzufügen</a>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

</body>

</html>