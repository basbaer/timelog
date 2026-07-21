<!doctype html>
<html lang="de">

@include('partials.head', ['title' => 'Log'])

<body>
    <form id="prepare-print-form" class="container border rounded my-3" method="POST" action="{{ route('workers.preparePrint.post', ['worker_id' => $worker->id]) }}">
        @csrf
        <input type="hidden" name="worker_id" value="{{ $worker->id }}">
        
        <h3 class="mt-2">{{ __('form.print_prepare') }}</h3>

        {{-- ================= PROJEKT-AUSWAHL ================= --}}
        <div class="container my-3 px-0">
            <h5 class="form-label d-block">{{ __('form.project') }}</h5>
 
            <div id="project-radio-list">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="project" id="project-all" value="all" checked>
                    <label class="form-check-label" for="project-all">
                        {{ __('form.all') }}
                    </label>
                </div>
 
                @foreach ($projects as $project)
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="project" id="project-{{ $project->id }}" value="{{ $project->id }}">
                        <label class="form-check-label" for="project-{{ $project->id }}">
                            {{ $project->title }}
                        </label>
                    </div>
                @endforeach
            </div>


        <div class="container my-3 px-0">
            <button type="submit" class="btn btn-primary">{{ __('form.print_view') }}</button>
        </div>
    </form>
</body>