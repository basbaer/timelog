@props(['log_entries'])

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
                        <div class="col-2"><strong>Ø Distanz</strong></div>
                    </div>
                    <hr class="my-0" />
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
                    <div class="col-1">{{ $entry->pause }}</div>
                    <div class="col-1">{{ $entry->sum }}</div>
                    <div class="col-2">{{ $entry->project_client }} ({{ $entry->project_location }})</div>
                    <div class="col-1">{{ $entry->bs_from }}</div>
                    <div class="col-1">{{ $entry->bs_to }}</div>
                    <div class="col-1">{{ $entry->loadings }}</div>
                    <div class="col-2">{{ $entry->averarge_distance }}</div>
                </div>
                <hr class="my-0" />
            @endif

            @if ($entry->working_type_name !== 'rueckezug')
                @if ($lastTableHeadIsRueckezug === null || $lastTableHeadIsRueckezug)
                    <x-worker-detail-forstwirt-table :log_entries="[$entry]" :show_partial_headers="$lastTableHeadIsRueckezug === null" :show_header_row="true" />
                    @php($lastTableHeadIsRueckezug = false)
                @else
                    <x-worker-detail-forstwirt-table :log_entries="[$entry]" :show_header_row="false" />
                @endif
            @endif
        @endforeach
    </x-worker-detail-day-card>
@endforeach
