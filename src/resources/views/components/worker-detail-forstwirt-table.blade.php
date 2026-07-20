@props([
    'log_entries',
    'worker_id',
    'isInsideOtherLog' => false,
    'show_partial_headers' => true,
    'show_header_row' => true,
])

@if ($show_header_row)
    <div class="row">
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
@foreach ($log_entries as $row)
    @if ($row->show_date)
        <hr class="my-0" />
    @else
        <div class="row">
            <div class="col-11 offset-1">
                <hr class="my-0" />
            </div>
        </div>
    @endif
    <div class="row align-items-center">
        <div class="col-1">
            @if ($row->show_date)
                {{ $row->weekday }},
                {{ $row->date }}
            @endif
        </div>
        <div class="col-1">{{ $row->start }}</div>
        <div class="col-1">{{ $row->end }}</div>
        <div class="col-1">{{ $row->pause ?? '-' }}</div>
        <div class="col-1">{{ $row->sum }}</div>
        <div class="col-2">{{ $row->project_client }} ({{ $row->project_location }})</div>
        <div class="col-1">{{ $row->working_type_name }}</div>

        <div class="col-3">{{ $row->comment }}</div>

        <div class="col-1 d-flex justify-content-center align-items-center ">
            <form action="{{ route('admin.worker.log.delete', ['worker_id' => $worker_id, 'log_id' => $row->id]) }}" method="POST"
                class="my-auto">
                @csrf
                @method('DELETE')
                <input type="hidden" name="delete_type" value="forstwirt">
                <button type="submit" class="btn btn-danger btn-sm p-0">Löschen</button>
            </form>
        </div>

    </div>
@endforeach
