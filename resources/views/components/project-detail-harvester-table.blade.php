@props(['logs'])

<div class="container-fluid">
    <div class="row mx-auto">BS = Betriebsstunden, FM = Festmeter</div>
    <div class="row mt-2">
        <div class="col"><strong>Datum</strong></div>
        <div class="col"><strong>Von</strong></div>
        <div class="col"><strong>Bis</strong></div>
        <div class="col"><strong>Pause</strong></div>
        <div class="col"><strong>Stunden</strong></div>
        <div class="col"><strong>Arbeiter</strong></div>
        <div class="col"><strong>BS von</strong></div>
        <div class="col"><strong>BS bis</strong></div>
        <div class="col"><strong>Anzahl</strong></div>
        <div class="col"><strong>FM (gesamt)</strong></div>
        <div class="col"><strong>FM (Tag)</strong></div>
        <hr class="my-0" />
    </div>
    @foreach ($logs as $log)
        <div class="row">
            <div class="col">{{ $log->date }}</div>
            <div class="col">{{ $log->start }}</div>
            <div class="col">{{ $log->end }}</div>
            <div class="col">{{ $log->pause }}</div>
            <div class="col">{{ $log->sum }}</div>
            <div class="col">{{ $log->user->first_name }} {{ $log->user->last_name }}</div>
            <div class="col">{{ $log->bs_from }}</div>
            <div class="col">{{ $log->bs_to }}</div>
            <div class="col">{{ $log->fm_amount }}</div>
            <div class="col">{{ $log->fm_total }}</div>
            <div class="col">{{ $log->fm_day }}</div>
        </div>
        <hr class="my-0" />
    @endforeach

</div>
