<!doctype html>
<html lang="de">

@include('partials.head', ['title' => 'Projekte'])

<body>
    @include('partials.admin_navbar', ['active' => 'projects'])
    
    <div class="container my-4">
        <h2>Übersicht Projekte</h2>
        <div class="list-group">
            <a href="dash-bau.html" class="list-group-item list-group-item-action">
                Ottendorf | 01/26 | Stängel
            </a>
            <a href="#" class="list-group-item list-group-item-action">Lauenstein | 11/25 | Förster xy</a>
            <a href="#" class="list-group-item list-group-item-action">Wallenfels | 09/25 | Förster ab</a>
            <a href="#" class="list-group-item list-group-item-action">Steinbach an der Heide | 09/25 | Methfessel</a>
        </div>
    </div>
    <div class="container my-4">
        <hr>
    </div>

    <div class="container">
        <div class="row ">
            <div class="col d-flex justify-content-start">
                <a href="#" class="btn btn-primary">Abgeschlossene Projekte</a>
            </div>
            <div class="col d-flex justify-content-end">
                <a href="dash-bau-new.html" class="btn btn-success">Neues Projekt anlegen</a>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

</body>

</html>