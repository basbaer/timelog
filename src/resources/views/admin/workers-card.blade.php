<!doctype html>
<html lang="de">

@include('partials.head', ['title' => 'Dashboard'])

<body>
    @include('partials.admin_navbar', ['active' => 'workers'])

    <div class="container mt-3 mx-auto d-flex justify-content-center">
        <div class="card" style="width: 18rem;">
            <div class="card-body">
                <h5 class="card-title">Birgit Bär</h5>
                <h6 class="card-subtitle mb-2 text-body-secondary">Forstwirt</h6>
                <table class="table">
                    <tbody>
                        <tr>
                            <td>Benutzername:</td>
                            <td>birgit.baer</td>
                        </tr>
                        <tr>
                            <td>Passwort:</td>
                            <td>wald2024!</td>
                        </tr>
                    </tbody>
                </table>
                <div class="d-flex justify-content-center">
                    <button type="button" class="btn btn-danger">Arbeiter löschen</button>
                </div>
            </div>
        </div>
    </div>
    <div class="container mt-3 d-flex justify-content-center">
        <a href="dash-workingHours.html" type="button" class="btn btn-primary">Zurück zur Stundenübersicht</a>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

</body>

</html>