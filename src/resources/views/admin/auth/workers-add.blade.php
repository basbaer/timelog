<!doctype html>
<html lang="de">

@include('partials.head', ['title' => 'Mitarbeiter'])

<body>
    <nav class="navbar navbar-expand bg-body-tertiary">
        <div class="container-fluid">

            <div class="collapse navbar-collapse justify-content-center" id="navbarNavDropdown">
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link " href="#">Projekte</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="#">Mitarbeiter</a>
                    </li>

                </ul>
            </div>
        </div>
    </nav>

    <form action="/admin/workers/add/request" method="POST">
        @csrf
        <div class="container mt-3">
            <div class="mb-3">
                <label for="first_name" class="form-label">Vorname des Arbeiters</label>
                <input name="first_name" type="text" class="form-control" id="first_name"
                    placeholder="Geben Sie den Vornamen ein">
            </div>
            <div class="mb-3">
                <label for="last_name" class="form-label">Nachname des Arbeiters</label>
                <input name="last_name" type="text" class="form-control" id="last_name"
                    placeholder="Geben Sie den Nachnamen ein">
            </div>

            @foreach ($roles as $role)
                @if ($loop->first)
                    <div class="mb-3">
                        <label for="dropDown" class="form-label">Arbeitsrolle</label>
                        <input type="hidden" name="role_id" id="role_id" value="{{ $role->id }}">
                        <div class="dropdown" id="dropDown">
                            <button class="btn btn-primary dropdown-toggle" type="button" data-bs-toggle="dropdown"
                                aria-expanded="false" id="dropDownButton" data-role-id="{{ $role->id }}" data-role-name="{{ $role->name }}">
                                {{ $role->name }}
                            </button>
                            <ul class="dropdown-menu">
                @else
                            <li><a class="dropdown-item" href="#" data-role-id="{{ $role->id }}" data-role-name="{{ $role->name }}">{{ $role->name }}</a></li>
                @endif

                @if ($loop->last)
                            </ul>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
        <div class="container mt-3 d-flex justify-content-end ">
            <button type="submit" class="btn btn-primary">Arbeiter hinzufügen</button>
        </div>
    </form>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>
    <script>
        $(document).ready(function() {
            $('.dropdown-menu').on('click', '.dropdown-item', function(e) {
                e.preventDefault();
                var newRoleId = $(this).data('role-id');
                var newRoleName = $(this).data('role-name');
                
                // Get current selected role from button
                var currentRoleId = $('#dropDownButton').data('role-id');
                var currentRoleName = $('#dropDownButton').data('role-name');
                
                // Add current role back to the list (before the clicked item)
                var currentRoleItem = '<li><a class="dropdown-item" href="#" data-role-id="' + currentRoleId + '" data-role-name="' + currentRoleName + '">' + currentRoleName + '</a></li>';
                $(this).parent().before(currentRoleItem);
                
                // Remove the clicked item from the list
                $(this).parent().remove();
                
                // Update button with new role
                $('#dropDownButton').text(newRoleName);
                $('#dropDownButton').data('role-id', newRoleId);
                $('#dropDownButton').data('role-name', newRoleName);
                
                // Update hidden input
                $('#role_id').val(newRoleId);
            });
        });
    </script>

</body>

</html>