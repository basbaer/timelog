@php
    $entryTitle = match ($savedLog->type) {
        'forstwirt' => $savedLog->workingType?->name ?? __('form.other'),
        'rueckezug' => 'Rückezug',
        default => 'Harvester',
    };

    $projectTitle = $savedLog->projectTitle;
    $worker_id = $savedLog->user_id;
    $log_id = $savedLog->id;
    $log_type = $savedLog->type;
@endphp

<div class="border rounded p-3 mb-3">
    <div class="mb-2">
        <div class="d-flex flex-row justify-content-between align-items-center mb-2 ">

            <div class="h4 mb-2">{{ $projectTitle }}</div>

            <div class="d-flex justify-content-center align-items-center gap-2">
                <a type="button"
                    class="btn btn-outline-primary btn-sm p-1 edit-log-button" title="Bearbeiten" aria-label="Bearbeiten"
                    data-log-type="{{ $savedLog->type }}"
                    data-edit-url="{{ route('log.' . $savedLog->type . '.edit', ['worker_id' => $worker_id, 'log_id' => $log_id]) }}">
                    
                    <i class="bi bi-pencil-square"></i>
                </a>
                <form action="{{ route('admin.worker.log.delete', ['worker_id' => $worker_id, 'log_id' => $log_id]) }}"
                    method="POST" class="my-auto">
                    @csrf
                    @method('DELETE')
                    <input type="hidden" name="delete_type" value="{{ $savedLog->type }}">
                    <button type="submit" class="btn btn-outline-danger btn-sm p-1" title="Löschen"
                        aria-label="Löschen">
                        <i class="bi bi-trash"></i>
                    </button>
                </form>
            </div>
        </div>
        <h3 class="h5 mb-2">{{ $entryTitle }}</h3>

        <div class="row g-3">
            <div class="col">
                {{ \Carbon\Carbon::parse($savedLog->start)->format('H:i') }} -
                {{ \Carbon\Carbon::parse($savedLog->end)->format('H:i') }}
                ({{ __('form.working_time') }}:
                {{ !empty($savedLog->sum) ? \Carbon\Carbon::parse($savedLog->sum)->format('H:i') : '-' }})
            </div>
        </div>
    </div>

    @if ($savedLog->type === 'harvester')
        <div class="row g-3">
            <div class="col-md-3 col-6">
                <div class="small text-muted">Betriebsstunden</div>
                <div>{{ $savedLog->bs_from ?? '-' }} - {{ $savedLog->bs_to ?? '-' }}</div>
            </div>
        </div>
        <div class="row g-3 mt-2">
            <div class="col-md-3 col-4">
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
    @elseif ($savedLog->type === 'rueckezug')
        <div class="row g-3">
            <div class="col-md-3 col-6">
                <div class="small text-muted">Betriebsstunden</div>
                <div>{{ $savedLog->bs_from ?? '-' }} - {{ $savedLog->bs_to ?? '-' }}</div>
            </div>
        </div>
        <div class="row g-3 mt-2">
            <div class="col-3">
                <div class="small text-muted">Fuhren</div>
                <div>{{ $savedLog->loadings ?? '-' }}</div>
            </div>
            <div class="col-9">
                <div class="small text-muted">Durchschnittliche Distanz (km)</div>
                <div>{{ $savedLog->average_distance ?? '-' }}</div>
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

<script>
    const editButton = document.getElementById('editButton');
    editButton.addEventListener('click', function(event) {
        event.preventDefault();
        const url = this.href;


    });

</script>
