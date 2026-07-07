<!doctype html>
<html lang="de">

@include('partials.head', ['title' => 'Dashboard'])

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

    <!-- Project Item -->
    <div class="container px-0 mt-3" id="projectItem">
        <h2>{{ $project->get('title') }}</h2>
        <div class="accordion mx-0 my-3 p-0" id="accordionProject">
            @foreach ($project->get('working_types') as $workingType)
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse{{ $workingType }}" aria-expanded="false"
                            aria-controls="collapseOne">
                            <div class="container p-0 m-0">
                                <div class="row p-0 m-0">
                                    <h5 class="col-8 p-0 m-0">{{ __('form.' . $workingType) }}</h5>
                                    <h5 class="col-4 text-end" id="{{ $workingType }}_sum">
                                        {{ $project->get($workingType)->get('sum') }} h</h5>
                                    <!-- Display the sum of hours and fm for harvester
                                    <h5 class="col-2 text-end border-end py-0 m-0" id="{{ $workingType }}_hours">18h
                                    </h5>
                                    <h5 class="col-2 text-start py-0 m-0" id="{{ $workingType }}_hours">12fm</h5>
                                    -->
                                </div>
                            </div>
                        </button>
                    </h2>
                    <div id="collapse{{ $workingType }}" class="accordion-collapse collapse "
                        data-bs-parent="#accordionProject">
                        <div class="accordion-body p-1">
                            @if ($workingType == 'harvester')
                                <x-project-detail-harvester-table :logs="$project->get($workingType)->get('logs')" />
                            @elseif ($workingType == 'rueckezug')
                                <x-project-detail-rueckezug-table :logs="$project->get($workingType)->get('logs')" />
                            @else
                                <x-project-detail-forstwirt-working-types-table :logs="$project->get($workingType)->get('logs')" />
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="container mt-4">
        <div class="row d-flex justify-content-between gap-3">
            @if (!$project->get('isClosed'))
                <form class="col-2 p-0" action="{{ route('admin.projects.close', $project->get('id')) }}"
                    method="POST">
                    @csrf
                    @method('PUT')
                    <button type="submit" class="btn btn-success">Projekt abschließen</button>
                </form>
            @else
                <div class="col"><strong>Projekt ist abgeschlossen</strong> (um Projekt erneut zu öffnen, klicke auf 'Projekt bearbeiten' & 'Projekt aktualisieren')
                </div>
            @endif

            <a href="{{ route('admin.projects.edit', $project->get('id')) }}" class="btn btn-primary col-2">Projekt
                bearbeiten</a>
        </div>
    </div>
    <div class="container my-4">
        <hr>
    </div>

    <div class="container d-flex justify-content-center">
        <a href="{{ route('admin.projects.overview') }}" class="btn btn-primary">Zurück zur Übersicht</a>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>

</body>

</html>
