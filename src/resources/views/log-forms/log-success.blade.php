<!doctype html>
<html lang="de">

@include('partials.head', ['title' => 'Log'])

<body>
    <div class="container d-flex justify-content-between align-items-center">
        <h1 class="mt-2">{{ $name }}</h1>
        <!-- Logout Button -->
        <div class="d-flex justify-content-end">
            <a href="/logout" class="btn btn-outline-danger">Logout</a>
        </div>
    </div>

    <div id="form-errors" class="container mt-3 success-message">
        <div class="alert alert-success" role="alert">
            {{ __('form.success_message') }}
        </div>
    </div>

    <div class="container mt-3 d-flex justify-content-center">
        <button class="btn btn-primary"> {{ __('form.edit_entry') }}</button>
    </div>
</body>