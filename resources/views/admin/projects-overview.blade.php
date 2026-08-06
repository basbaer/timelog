<!doctype html>
<html lang="de">

@include('partials.head', ['title' => 'Projekte'])

<body>
    @include('partials.admin_navbar', ['active' => 'projects'])

    @if (session('success'))
        <div class="container mt-3">
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    @endif
    
    <div class="container my-4">
        @if (! $closed)
        <h2>Übersicht Projekte</h2>
        @else
        <h2>Übersicht abgeschlossene Projekte</h2>    
        @endif
        <div class="list-group">
            @foreach ($projects as $project)
                <a href="{{ route('admin.project.detail', $project->id) }}" class="list-group-item list-group-item-action">
                    {{ $project->location }} | {{ $project->date->format('m/Y') }} | {{ $project->client }}
                </a>
            @endforeach
        </div>
    </div>
    <div class="container my-4">
        <hr>
    </div>

    <div class="container">
        <div class="row ">
            <div class="col d-flex justify-content-start">
                @if (! $closed)
                <a href="{{ route('admin.projects.closed') }}" class="btn btn-primary">Abgeschlossene Projekte</a>
                @else
                <a href="{{ route('admin.projects.overview') }}" class="btn btn-primary">Übersicht offene Projekte</a>
                @endif
            </div>
            <div class="col d-flex justify-content-end">
                <a href="{{ route('admin.projects.add') }}" class="btn btn-success">Neues Projekt anlegen</a>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI"
        crossorigin="anonymous"></script>

</body>

</html>