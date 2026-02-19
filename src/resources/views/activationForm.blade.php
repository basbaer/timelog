<!doctype html>
<html lang="de">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
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
        <form action="/login" method="POST">
            @csrf
            <div class="mb-3">
                <label for="username" class="form-label">Benutzername</label>
                <input name="username" type="text" class="form-control" id="username"
                    placeholder="Geben Sie Ihren Benutzernamen ein">
            </div>
            <div class="mb-3">
                <label for="activation_code" class="form-label">Aktivierungscode</label>
                <input name="activation_code" type="text" class="form-control" id="activation_code"
                    placeholder="Geben Sie Ihren Aktivierungscode ein">
            </div>
            <div class="mb-3">
                <label for="new_password" class="form-label">Passwort erstellen</label>
                <div class="input-group">
                    <input name="new_password" type="password" class="form-control" id="new_password"
                        placeholder="Geben Sie Ihr Passwort ein">
                    <span class="input-group-text" id="togglePassword" role="button"
                        aria-label="Passwort anzeigen/verbergen">
                        <i class="bi bi-eye-slash"></i>
                    </span>
                </div>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Passwort bestätigen</label>
                <div class="input-group">
                    <input name="confirm_password" type="password" class="form-control" id="confirm_password"
                        placeholder="Bestätigen Sie Ihr Passwort">
                    <span class="input-group-text" id="toggleConfirmPassword" role="button"
                        aria-label="Passwort anzeigen/verbergen">
                        <i class="bi bi-eye-slash"></i>
                    </span>
                </div>
            </div>
            <div class="form-control mb-3 border-0 ps-1">
                <label class="label cursor-pointer justify-start">
                    <input type="checkbox" name="remember" class="checkbox">
                    <span class="label-text">Eingeloggt bleiben</span>
                </label>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">Anmelden</button>
            </div>

            <div class="mt-3 text-center">
                <a href="/" class="text-decoration-none">Mit Passwort anmelden</a>
            </div>
        </form>

        <script>
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#new_password');
            const icon = togglePassword.querySelector('i');

            togglePassword.addEventListener('click', () => {
                const isPassword = password.getAttribute('type') === 'password';
                password.setAttribute('type', isPassword ? 'text' : 'password');
                icon.classList.toggle('bi-eye', isPassword);
                icon.classList.toggle('bi-eye-slash', !isPassword);
            });

            const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
            const confirmPassword = document.querySelector('#confirm_password');
            const confirmIcon = toggleConfirmPassword.querySelector('i');

            toggleConfirmPassword.addEventListener('click', () => {
                const isPassword = confirmPassword.getAttribute('type') === 'password';
                confirmPassword.setAttribute('type', isPassword ? 'text' : 'password');
                confirmIcon.classList.toggle('bi-eye', isPassword);
                confirmIcon.classList.toggle('bi-eye-slash', !isPassword);
            });
        </script>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
</script>

</body>

</html>
