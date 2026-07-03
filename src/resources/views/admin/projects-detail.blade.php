<!doctype html>
<html lang="de">

@include('partials.head', ['title' => 'Dashboard'])

<body>
    @include('partials.admin_navbar', ['active' => 'projects'])


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
                                    <h5 class="col-4 text-end" id="{{ $workingType }}_sum">{{ $project->get($workingType)->get('sum') }} h</h5>
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
            <div class="form-check col-6">
                <input class="form-check-input" type="checkbox" value="" id="defaultCheck1">
                <label class="form-check-label" for="defaultCheck1">
                    Projekt abgeschlossen
                </label>
            </div>
            <a href="dash-bau-overview.html" class="btn btn-primary col-4">Projekt bearbeiten</a>
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
