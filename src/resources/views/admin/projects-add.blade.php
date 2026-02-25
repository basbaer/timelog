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
    <nav class="navbar navbar-expand bg-body-tertiary">
        <div class="container-fluid">

            <div class="collapse navbar-collapse justify-content-center" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Projekte</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Mitarbeiter</a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>
    <div class="container my-4">
        <h2>Neues Projekt anlegen</h2>
        <form>
            <div class="mb-3">
                <label for="location" class="form-label">Ort</label>
                <input type="text" class="form-control" id="location" placeholder="Geben Sie den Ort ein">
            </div>
            <div class="mb-3">
                <label for="startDate" class="form-label">Startdatum</label>
                <input type="date" class="form-control" id="startDate">
            </div>
            <div class="mb-3">
                <label for="forster" class="form-label">Förster/Waldbesitzer</label>
                <input type="text" class="form-control" id="forster"
                    placeholder="Geben Sie den Förster oder Waldbesitzer ein">
            </div>
            <div class="mb-3">
                <label for="workerSelection" class="form-label">Mitarbeiter auswählen</label>
                <ul class="list-group" id="workerSelection">
                    <li class="list-group-item">
                        <input class="form-check-input me-1" type="checkbox" value="" id="firstCheckbox">
                        <label class="form-check-label" for="firstCheckbox">Harvester</label>
                    </li>
                    <li class="list-group-item">
                        <input class="form-check-input me-1" type="checkbox" value="" id="secondCheckbox">
                        <label class="form-check-label" for="secondCheckbox">Rückezug</label>
                    </li>
                    <li class="list-group-item">
                        <input class="form-check-input me-1" type="checkbox" value="" id="thirdCheckbox">
                        <label class="form-check-label" for="thirdCheckbox">Forstwirt</label>
                    </li>
                </ul>
            </div>
            <div class="mb-3 d-flex justify-content-end">
                <button type="submit" class="btn btn-primary">Projekt erstellen</button>
            </div>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

</body>

</html>