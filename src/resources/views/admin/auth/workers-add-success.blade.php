<!doctype html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Dashboard</title>
    <link rel="icon" href="{{ asset('media/icons/wood.svg') }}" type="image/svg+xml">
</head>

<body>
    <div class="container mt-3 rounded-3 bg-success-subtle d-flex justify-content-center p-3">
        <h4 class="text-success">Der Arbeiter wurde erfolgreich hinzugefügt!</h4>

    </div>
    <div class="container mt-3 mx-auto d-flex justify-content-center">
        <div class="card d-flex"">
            <div class="card-body">
                <h5 class="card-title">
                    {{ session()->get('result')['user']['first_name'] . ' ' . session()->get('result')['user']['last_name'] }}
                </h5>
                <h6 class="card-subtitle mb-2 text-body-secondary"> {{ session()->get('result')['role'] }}</h6>
                <p class="card-text">Dem Arbeiter Folgendes mitteilen:</p>
                <table class="table">
                    <tbody>
                        <tr>
                            <td>Benutzername:</td>
                            <td> {{ session()->get('result')['user']['username'] }}</td>
                        </tr>
                        <tr>
                            <td>Aktivierungscode:</td>
                            <td>{{ session()->get('result')['user']['activation_code'] }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="container mt-3 d-flex justify-content-center">
        @auth
            <!-- if admin is logge in, return to Worker Overview -->
            <a href="/workers" type="button" class="btn btn-primary">Bestätigen</a>
        @else
            <!-- if admin is not logged in, return to Login Page -->
            <a href="/" type="button" class="btn btn-primary">Bestätigen</a>
        @endauth

    </div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>

</body>

</html>
