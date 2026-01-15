<!doctype html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <title>Gustl Schweitzer</title>
    <link rel="icon" href="../media/icons/wood.svg" type="image/svg+xml">
</head>
 <style>
body {
  background-image: url('../media/pictures/forrest.jpg');
  background-repeat: no-repeat;
  background-attachment: fixed;
  background-size: cover;
  background-position: center;
}
</style> 
<div class="container d-flex justify-content-center align-items-center" style="height: 100vh; padding-bottom: 150px;">
    <div class="card p-4 m" style="min-width: 300px; max-width: 400px; width: 100%;">
        <h3 class="card-title text-center mb-4">Anmelden</h3>
        <form action="/timelog" method="POST">
            @csrf
            <div class="mb-3">
                <label for="username" class="form-label">Benutzername</label>
                <input name="username" type="text" class="form-control" id="username" placeholder="Geben Sie Ihren Benutzernamen ein">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Passwort</label>
                <input name="password" type="password" class="form-control" id="password" placeholder="Geben Sie Ihr Passwort ein">
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Anmelden</button>
            </div>
        </form>
    </div>
</div>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

</body>

</html>
