@props(['log_entries', 'show_partial_headers' => true, 'show_header_row' => true])

@if ($show_header_row)
    <div class="row mt-2">
        <div class="col-1"><strong>
                @if ($show_partial_headers)
                    Datum
                @endif
            </strong></div>
        <div class="col-1"><strong>
                @if ($show_partial_headers)
                    Von
                @endif
            </strong></div>
        <div class="col-1"><strong>
                @if ($show_partial_headers)
                    Bis
                @endif
            </strong></div>
        <div class="col-1"><strong>
                @if ($show_partial_headers)
                    Pause
                @endif
            </strong></div>
        <div class="col-1"><strong>
                @if ($show_partial_headers)
                    Gesamt
                @endif
            </strong></div>
        <div class="col-2"><strong>
                @if ($show_partial_headers)
                    Baustelle
                @endif
            </strong></div>
        <div class="col-1"><strong>Arbeitsart</strong></div>
        <div class="col-4"><strong>Anmerkung</strong></div>
    </div>
@endif
<div class="row">
    @foreach ($log_entries as $row)
        <div class="col-1">
            @if ($row->show_date)
                {{ $row->weekday }},
                {{ $row->date }}
            @endif
        </div>
        <div class="col-1">{{ $row->start }}</div>
        <div class="col-1">{{ $row->end }}</div>
        <div class="col-1">{{ $row->pause }}</div>
        <div class="col-1">{{ $row->sum }}</div>
        <div class="col-2">{{ $row->project_client }} ({{ $row->project_location }})</div>
        <div class="col-1">{{ $row->working_type_name }}</div>
        <div class="col-4">{{ $row->comment }}</div>
    @endforeach
</div>
