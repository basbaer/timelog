@php
    $entryTitle = match ($savedLog->entry_label) {
        'forstwirt' => $savedLog->workingType?->name ?? __('form.other'),
        'rueckezug' => 'Rückezug',
        default => 'Harvester',
    };
@endphp

<div class="border rounded p-3 mb-3">
    <div class="mb-2">
        <h3 class="h5 mb-0">{{ $entryTitle }}</h3>
        <div class="row g-3">
            <div class="col">
                {{ \Carbon\Carbon::parse($savedLog->start)->format('H:i') }} -
                {{ \Carbon\Carbon::parse($savedLog->end)->format('H:i') }}
                ({{ __('form.working_time') }}:
                {{ !empty($savedLog->sum) ? \Carbon\Carbon::parse($savedLog->sum)->format('H:i') : '-' }})
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
    @elseif ($savedLog->entry_label === 'rueckezug')
        <div class="row g-3">
            <div class="col-md-3 col-6">
                <div class="small text-muted">Betriebsstunden</div>
                <div>{{ $savedLog->bs_from ?? '-' }} - {{ $savedLog->bs_to ?? '-' }}</div>
            </div>
        </div>
        <div class="row g-3 mt-2">
            <div class="col-md-3 col-6">
                <div class="small text-muted">Fuhren</div>
                <div>{{ $savedLog->loadings ?? '-' }}</div>
            </div>
            <div class="col-md-6 col-6">
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
