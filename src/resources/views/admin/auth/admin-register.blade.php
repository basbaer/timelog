<!doctype html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Administrator erstellen</title>
    <link rel="icon" href="../media/icons/wood.svg" type="image/svg+xml">
</head>

<body>
    <form action="/workers/add/request" method="POST">
        @csrf
        <div class="container mt-3">
            <div class="mb-3">
                <label for="first_name" class="form-label">Vorname</label>
                <input name="first_name" type="text" class="form-control" id="first_name"
                    placeholder="Geben Sie den Vornamen ein">
            </div>
            <div class="mb-3">
                <label for="last_name" class="form-label">Nachname</label>
                <input name="last_name" type="text" class="form-control" id="last_name"
                    placeholder="Geben Sie den Nachnamen ein">
            </div>
            <input type="hidden" name="role_id" id="role_id" value=" {{ $adminId }}">

        </div>
        <div class="container mt-3 d-flex justify-content-end ">
            <button type="submit" class="btn btn-primary">Registrieren</button>
        </div>
    </form>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>

</body>

</html>
