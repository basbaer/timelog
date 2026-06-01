<!doctype html>
<html lang="de">

@include('partials.head', ['title' => 'Log'])

<body>
    @include('partials.log_header', ['name' => $name])

    @include('partials.log_form_errors', ['errors' => $errors])

    <form id="forstwirt-log-form" class="container" method="POST" action="{{ route('log.forstwirt.store') }}">
        @csrf
        <input type="hidden" name="user_id" value="{{ $user_id }}">
        @php
            $workTypes = [
                'motorsage' => __('form.motorsage'),
                'freischneider' => __('form.freischneider'),
                'seilmaschine' => __('form.seilmaschine'),
                'messkluppe' => __('form.messkluppe'),
                'reparatur' => __('form.reparatur'),
                'other' => __('form.other'),
            ];
            $workTypeCount = count($workTypes);
            $today = now()->format('Y-m-d');
        @endphp
        <div class="container my-3 px-0">
            <label for="date" class="form-label">{{ __('form.date') }}</label>
            <input id="date" name="log_date" class="form-control" type="date" lang="de"
                value="{{ old('log_date', $today) }}" @unless ($isAdmin) readonly @endunless />
        </div>

        <div class="accordion" id="accordionProjects">
            @foreach ($projects as $project)
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapse{{ $loop->index }}" aria-expanded="false"
                            aria-controls="collapse{{ $loop->index }}">
                            {{ $project->location }} | {{ $project->date->format('m/Y') }} | {{ $project->client }}
                        </button>
                    </h2>
                    <div id="collapse{{ $loop->index }}" class="accordion-collapse collapse"
                        data-bs-parent="#accordionProjects">

                        <div class="accordion-body px-2">
                            <div id="work-type-entries-{{ $loop->index }}">
                                @for ($entryIndex = 0; $entryIndex < $workTypeCount; $entryIndex++)
                                    <x-forstwirt-work-type :project-index="$loop->index" :project-id="$project->id"
                                        :entry-index="$entryIndex" :work-types="$workTypes"
                                        :hidden="$entryIndex > 0" />
                                @endfor
                            </div>

                            <hr class="mx-3">

                            <div class="mt-2 mx-3">
                                <button id="add-work-type-button-{{ $loop->index }}" class="btn btn-primary"
                                    type="button" data-project-index="{{ $loop->index }}"
                                    onclick="showWorkTypeEntry({{ $loop->index }}, this)">
                                    {{ __('form.add_work_type') }}
                                </button>
                            </div>
                        </div>

                    </div>
                </div>
            @endforeach
        </div>

        <div class="container d-flex justify-content-center">
            <button class="btn btn-success my-3" type="submit">Eintrag speichern</button>
        </div>
    </form>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous">
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            initForstwirtWorkTypeEntries();
        });
    </script>
</body>

</html>
