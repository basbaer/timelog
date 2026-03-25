<!doctype html>
<html lang="de">

@include('partials.head', ['title' => 'Dashboard'])

<body>
    @include('partials.admin_navbar', ['active' => 'projects'])
    
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