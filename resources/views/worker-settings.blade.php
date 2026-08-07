<!doctype html>
<html lang="de">



@include('partials.head', ['title' => 'Gustl Schweitzer', 'withBootstrapIcons' => true])

<div class="container d-flex justify-content-center align-items-center" style="height: 100vh; padding-bottom: 150px;">
    <div class="card p-4 m" style="min-width: 300px; max-width: 400px; width: 100%;">
        <h3 class="card-title text-center mb-2">{{ __('labels.password_change') }}</h3>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('worker.password.change', ['worker_id' => $worker_id]) }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="old_password" class="form-label">{{ __('labels.old_password') }}</label>
                <div class="input-group">
                    <input name="old_password" type="password" class="form-control" id="old_password"
                        placeholder="{{ __('labels.hint_old_password') }}">
                    <span class="input-group-text" id="toggleOldPassword" role="button"
                        aria-label="Passwort anzeigen/verbergen">
                        <i class="bi bi-eye-slash"></i>
                    </span>
                </div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">{{ __('labels.new_password') }}</label>
                <div class="input-group">
                    <input name="password" type="password" class="form-control" id="password"
                        placeholder="{{ __('labels.hint_password') }}">
                    <span class="input-group-text" id="togglePassword" role="button"
                        aria-label="Passwort anzeigen/verbergen">
                        <i class="bi bi-eye-slash"></i>
                    </span>
                </div>
            </div>
            <div class="mb-3">
                <label for="password_confirmation"
                    class="form-label">{{ __('labels.hint_password_confirmation') }}</label>
                <div class="input-group">
                    <input name="password_confirmation" type="password" class="form-control" id="password_confirmation"
                        placeholder="{{ __('labels.hint_password_confirmation') }}">
                    <span class="input-group-text" id="toggleConfirmPassword" role="button"
                        aria-label="Passwort anzeigen/verbergen">
                        <i class="bi bi-eye-slash"></i>
                    </span>
                </div>
            </div>

            <div class="d-grid">
                <button type="submit" class="btn btn-primary">{{ __('labels.password_change') }}</button>
            </div>
        </form>

        <!-- Back button -->
    <div class="mt-3 text-center">
        <a href="/" class="btn btn-outline-secondary">{{ __('labels.back') }}</a>
    </div>

        <script>
            const togglePassword = document.querySelector('#togglePassword');
            const password = document.querySelector('#password');
            const icon = togglePassword.querySelector('i');

            togglePassword.addEventListener('click', () => {
                const isPassword = password.getAttribute('type') === 'password';
                password.setAttribute('type', isPassword ? 'text' : 'password');
                icon.classList.toggle('bi-eye', isPassword);
                icon.classList.toggle('bi-eye-slash', !isPassword);
            });

            const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
            const confirmPassword = document.querySelector('#password_confirmation');
            const confirmIcon = toggleConfirmPassword.querySelector('i');

            toggleConfirmPassword.addEventListener('click', () => {
                const isPassword = confirmPassword.getAttribute('type') === 'password';
                confirmPassword.setAttribute('type', isPassword ? 'text' : 'password');
                confirmIcon.classList.toggle('bi-eye', isPassword);
                confirmIcon.classList.toggle('bi-eye-slash', !isPassword);
            });

            const toggleOldPassword = document.querySelector('#toggleOldPassword');
            const oldPassword = document.querySelector('#old_password');
            const oldIcon = toggleOldPassword.querySelector('i');

            toggleOldPassword.addEventListener('click', () => {
                const isPassword = oldPassword.getAttribute('type') === 'password';
                oldPassword.setAttribute('type', isPassword ? 'text' : 'password');
                oldIcon.classList.toggle('bi-eye', isPassword);
                oldIcon.classList.toggle('bi-eye-slash', !isPassword);
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
