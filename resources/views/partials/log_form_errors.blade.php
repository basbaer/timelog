<div id="form-errors" class="container alert alert-danger @if (!$errors->any()) d-none @endif">
    <ul class="mb-0" id="form-errors-list">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
