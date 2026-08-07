<!doctype html>
<html lang="de">

@push('head')
    <style>
        body {
            background-image: url('{{ asset('media/pictures/forrest.jpg') }}');
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: cover;
            background-position: center;
        }
    </style>
@endpush

@include('partials.head', ['title' => 'Gustl Schweitzer', 'withBootstrapIcons' => true])

<div class="container d-flex justify-content-center align-items-center" style="height: 100vh; padding-bottom: 150px;">
    <div class="card p-4 m" style="min-width: 300px; max-width: 400px; width: 100%;">
        <h3 class="card-title text-center mb-2">{{ __('labels.login') }}</h3>
               @include('partials/language_switcher')
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="{{ route('password-reset') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label for="username" class="form-label">{{ __('labels.username') }}</label>
                <input name="username" type="text" class="form-control" id="username"
                    placeholder="{{ __('labels.hint_username') }}">
            </div>

            <div class="my-2 p-3 text-primary-emphasis bg-primary-subtle border border-primary-subtle rounded-3">{{ __('labels.link_password_reset_text') }}</div>
            
            <div class="d-grid">
                <button type="submit" class="btn btn-primary">{{ __('labels.link_password_reset') }}</button>
            </div>

            <div class="mt-3 text-center">
                <a href="/" class="text-decoration-none">{{ __('labels.link_login_password') }}</a>
            </div>
        </form>

    </div>
</div>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
</script>

</body>

</html>
