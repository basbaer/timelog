<!doctype html>
<html lang="de">

@include('partials.head', ['title' => 'Administrator erstellen'])

<body>
    <form action="createAdmin/request" method="POST">
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
