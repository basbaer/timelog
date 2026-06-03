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

    @php
        use Carbon\Carbon;
    @endphp

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
                            <div class="border rounded p-3 mb-3">
                                <div class="mb-2">
                                    <h3 class="h5 mb-0">
                                        {{ $savedLog->entry_label === 'forstwirt' ? $savedLog->workingType?->name ?? __('form.other') : 'Harvester' }}
                                    </h3>
                                    <div class="row g-3">
                                        <div class="col">
                                            {{ Carbon::create($savedLog->start)->format('H:i') }} -
                                            {{ Carbon::create($savedLog->end)->format('H:i') }}
                                            ({{ __('form.working_time') }}:
                                            {{ Carbon::parse($savedLog->sum)->format('H:i') ?? '-' }})
                                        </div>
                                    </div>
                                </div>

                                @if ($savedLog->entry_label === 'harvester')
                                    <div class="row g-3">
                                        <div class="col-md-3 col-6">
                                            <div class="small text-muted">Betriebsstunden</div>
                                            <div>{{ $savedLog->bs_from ?? '-' }} - {{ $savedLog->bs_to ?? '-' }}</div>
                                        </div>
                                    </div>
                                    <div class="row g-3 mt-2">
                                        <div class="cold-md-3 col-4">
                                            <div class="small text-muted">Stückzahl</div>
                                            <div>{{ $savedLog->fm_amount ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-3 col-4">
                                            <div class="small text-muted">Gesamt fm</div>
                                            <div>{{ $savedLog->fm_total ?? '-' }}</div>
                                        </div>
                                        <div class="col-md-3 col-4">
                                            <div class="small text-muted">fm/Tag</div>
                                            <div>{{ $savedLog->fm_day ?? '-' }}</div>
                                        </div>

                                    </div>
                                @else
                                    <div class="row g-2">
                                        <div class="col">
                                            <div class="small text-muted">{{ __('form.comment') }}</div>
                                            <div>{{ $savedLog->comment ?: '-' }}</div>
                                        </div>

                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <form id="success-form" action="{{ route($deleteRouteName, ['log_id' => $log_id]) }}" method="POST"
        class="mt-3">
        @csrf
        @method('DELETE')
        <div class="container d-flex justify-content-center">
            <button class="btn btn-danger"> {{ __('form.edit_entry') }}</button>
        </div>
    </form>

</body>
