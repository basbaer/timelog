@props(['log_entries', 'worker_id', 'isAdmin' => true])

<div class="container-fluid justify-content-center d-flex">
    <div class="mx-auto">BS = Betriebsstunden, FM = Festmeter</div>
</div>
@foreach ($log_entries->groupBy('date_raw') as $dayEntries)
    <x-worker-detail-day-card>
        @php($lastTableHeadIsRueckezug = null)
        @foreach ($dayEntries as $entry)
            @if ($entry->working_type_name === 'rueckezug')
                @if ($lastTableHeadIsRueckezug === null || !$lastTableHeadIsRueckezug)
                    @php($show_partial_headers = $lastTableHeadIsRueckezug === null)
                    @php($lastTableHeadIsRueckezug = true)
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
                        <div class="col-1"><strong>BS von</strong></div>
                        <div class="col-1"><strong>BS bis</strong></div>
                        <div class="col-1"><strong>Fuhren</strong></div>
                        <div class="col-1"><strong>Ø Distanz</strong></div>
                    </div>
                    @if ($entry->show_date)
                        <hr class="my-0" />
                    @else
                        <div class="row">
                            <div class="col-11 offset-1">
                                <hr class="my-0" />
                            </div>
                        </div>
                    @endif
                @endif

                <div class="row">
                    <div class="col-1">
                        @if ($entry->show_date)
                            {{ $entry->weekday }},
                            {{ $entry->date }}
                        @endif
                    </div>
                    <div class="col-1">{{ $entry->start }}</div>
                    <div class="col-1">{{ $entry->end }}</div>
                    <div class="col-1">{{ $entry->pause ?? '-' }}</div>
                    <div class="col-1">{{ $entry->sum }}</div>
                    <div class="col-2">{{ $entry->project_client }} ({{ $entry->project_location }})</div>
                    <div class="col-1">{{ $entry->bs_from }}</div>
                    <div class="col-1">{{ $entry->bs_to }}</div>
                    <div class="col-1">{{ $entry->loadings }}</div>
                    <div class="col-1">{{ $entry->average_distance }}</div>

                    @if ($isAdmin)
                        <div class="col-1 d-flex justify-content-center align-items-center gap-1">
                            <a href="{{ route('log.rueckezug.edit', ['worker_id' => $worker_id, 'log_id' => $entry->id]) }}"
                                class="btn btn-outline-primary btn-sm p-1" title="Bearbeiten" aria-label="Bearbeiten">
                                <i class="bi bi-pencil-square"></i>
                            </a>
                            <form action="{{ route('log.rueckezug.delete', ['worker_id' => $worker_id, 'log_id' => $entry->id]) }}"
                                method="POST" class="my-auto">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="delete_type" value="rueckezug">
                                <button type="submit" class="btn btn-outline-danger btn-sm p-1" title="Löschen"
                                    aria-label="Löschen">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    @endif

                </div>
                <div class="row">
                    <div class="col-11 offset-1">
                        <hr class="my-0" />
                    </div>
                </div>
            @endif

            @if ($entry->working_type_name !== 'rueckezug')
                @if ($lastTableHeadIsRueckezug === null || $lastTableHeadIsRueckezug)
                    <x-worker-detail-forstwirt-table :log_entries="[$entry]" :worker_id="$worker_id" :show_partial_headers="$lastTableHeadIsRueckezug === null"
                        :show_header_row="true" :isAdmin="$isAdmin"/>
                    @php($lastTableHeadIsRueckezug = false)
                @else
                    <x-worker-detail-forstwirt-table :log_entries="[$entry]" :worker_id="$worker_id" :show_header_row="false" :isAdmin="$isAdmin"/>
                @endif
            @endif
        @endforeach
    </x-worker-detail-day-card>
@endforeach
