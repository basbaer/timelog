<!doctype html>
<html lang="de">

@include('partials.head', ['title' => 'Log'])

<body>
    @include('partials.log_header', ['name' => $name, 'worker_id' => $user_id])

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
                    $totalStart = $projectGroup['totalStart'];
                    $totalEnd = $projectGroup['totalEnd'];
                    $totalSum = $projectGroup['totalSum'];
                @endphp

                <div class="card mb-3">
                    <div class="card-header">
                        <strong>{{ $project->location }}</strong> | {{ $project->date->format('m/Y') }} |
                        {{ $project->client }}
                    </div>
                    <div class="card-body">
                        <div class="h5"> {{ __('form.total_hours') }}</div>
                        <div class="row g-3 mb-2">
                            <div class="col">
                                {{ \Carbon\Carbon::parse($totalStart)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($totalEnd)->format('H:i') }}
                                ({{ __('form.working_time') }}:
                                {{ !empty($totalSum) ? \Carbon\Carbon::parse($totalSum)->format('H:i') : '-' }})
                            </div>
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
