@props(['log_entries', 'worker_id', 'isAdmin' => true])

<div class="container-fluid justify-content-center d-flex">
    <div class="mx-auto">BS = Betriebsstunden, FM = Festmeter</div>
</div>
@foreach ($log_entries->groupBy('date_raw') as $dayEntries)
    <x-worker-detail-day-card>
        @php($lastTableHeadIsHarvester = null)
        @foreach ($dayEntries as $entry)
            @if ($entry->working_type_name === 'harvester')
                @if ($lastTableHeadIsHarvester === null || !$lastTableHeadIsHarvester)
                    @php($show_partial_headers = $lastTableHeadIsHarvester === null)
                    @php($lastTableHeadIsHarvester = true)
                    <div class="row mt-0">
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
                        <div class="col-1"><strong>
                                @if ($show_partial_headers)
                                    Baustelle
                                @endif
                            </strong></div>
                        <div class="col-1"><strong>BS von</strong></div>
                        <div class="col-1"><strong>BS bis</strong></div>
                        <div class="col-1"><strong>Anzahl</strong></div>
                        <div class="col-1"><strong>FM (gesamt)</strong></div>
                        <div class="col-1"><strong>FM (Tag)</strong></div>
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
                    <div class="col-1">{{ $entry->project_client }} ({{ $entry->project_location }})</div>
                    <div class="col-1">{{ $entry->bs_from }}</div>
                    <div class="col-1">{{ $entry->bs_to }}</div>
                    <div class="col-1">{{ $entry->fm_amount }}</div>
                    <div class="col-1">{{ $entry->fm_total }}</div>
                    <div class="col-1">{{ $entry->fm_day }}</div>
                    @if ($isAdmin)
                        <div class="col-1 d-flex justify-content-center align-items-center ">
                            <form
                                action="{{ route('admin.worker.log.delete', ['worker_id' => $worker_id, 'log_id' => $entry->id]) }}"
                                method="POST" class="my-auto">
                                @csrf
                                @method('DELETE')
                                <input type="hidden" name="delete_type" value="harvester">
                                <button type="submit" class="btn btn-danger btn-sm p-0">Löschen</button>
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

            @if ($entry->working_type_name !== 'harvester')
                @if ($lastTableHeadIsHarvester === null || $lastTableHeadIsHarvester)
                    <x-worker-detail-forstwirt-table :log_entries="[$entry]" :worker_id="$worker_id" :show_partial_headers="$lastTableHeadIsHarvester === null"
                        :show_header_row="true" :isAdmin="$isAdmin"/>
                    @php($lastTableHeadIsHarvester = false)
                @else
                    <x-worker-detail-forstwirt-table :log_entries="[$entry]" :worker_id="$worker_id" :show_header_row="false" :isAdmin="$isAdmin"/>
                @endif
            @endif
        @endforeach
    </x-worker-detail-day-card>
@endforeach
