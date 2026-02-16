
<!doctype html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Dashboard</title>
</head>

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

    <div class="container mt-3 rounded-3 bg-success-subtle d-flex justify-content-center p-3">
        <h4 class="text-success">Der Arbeiter wurde erfolgreich hinzugefügt!</h4>

    </div>
    <div class="container mt-3 mx-auto d-flex justify-content-center">
        <div class="card" style="width: 18rem;">
            <div class="card-body">
                <h5 class="card-title">{{ session()->get('result')['user']['first_name'] . " " . session()->get('result')['user']['last_name'] }}</h5>
                <h6 class="card-subtitle mb-2 text-body-secondary"> {{ session()->get('result')['role'] }}</h6>
                <p class="card-text">Dem Arbeiter Folgendes mitteilen:</p>
                <table class="table">
                    <tbody>
                        <tr>
                            <td>Benutzername:</td>
                            <td> {{ session()->get('result')['user']['username'] }}</td>
                        </tr>
                        <tr>
                            <td>Passwort:</td>
                            <td>{{session()->get('result')['password'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>  
    </div>
    <div class="container mt-3 d-flex justify-content-center">
        <a href="dash-worker.html" type="button" class="btn btn-primary">Zurück zur Arbeiterübersicht</a>
    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

</body>

</html>