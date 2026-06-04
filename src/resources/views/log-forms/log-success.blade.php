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

    @if (!empty($logOverview) && count($logOverview))
        <div class="container mt-4">
            <h2 class="mb-3">{{ __('form.date') }}: {{ $logDate }}</h2>

            @foreach ($logOverview as $projectGroup)
                @php
                    $project = $projectGroup['project'];
                    $projectLogs = $projectGroup['logs'];
                @endphp

                <div class="card mb-3">
                    <div class="card-header">
                        <strong>{{ $project->location }}</strong> | {{ $project->date->format('m/Y') }} |
                        {{ $project->client }}
                    </div>
                    <div class="card-body">
                        @foreach ($projectLogs as $savedLog)
                            @include('log-forms.partials.log-summary-item', ['savedLog' => $savedLog])
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <form id="success-form" action="{{ route($deleteRouteName, ['worker_id' => $user_id]) }}" method="POST"
        class="mt-3">
        @csrf
        @method('DELETE')
        <div class="container d-flex justify-content-center mb-4">
            <button class="btn btn-danger" type="submit"> {{ __('form.edit_entry') }}</button>
        </div>
    </form>

</body>
