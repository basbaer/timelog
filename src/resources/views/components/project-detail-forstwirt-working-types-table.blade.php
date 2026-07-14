@props(['logs'])

<div class="container-fluid">
    <div class="row mt-2">
        <div class="col-1"><strong>Datum</strong></div>
        <div class="col-1"><strong>Von</strong></div>
        <div class="col-1"><strong>Bis</strong></div>
        <div class="col-1"><strong>Pause</strong></div>
        <div class="col-1"><strong>Stunden</strong></div>
        <div class="col-1"><strong>Arbeiter</strong></div>
        <div class="col-6"><strong>Kommentar</strong></div>
        <hr class="my-0" />
    </div>
    @foreach ($logs as $log)
        <div class="row">
            <div class="col-1">{{ $log->date }}</div>
            <div class="col-1">{{ $log->start }}</div>
            <div class="col-1">{{ $log->end }}</div>
            <div class="col-1">{{ $log->pause }}</div>
            <div class="col-1">{{ $log->sum }}</div>
            <div class="col-1">{{ $log->user->first_name }} {{ $log->user->last_name }}</div>
            <div class="col-6">{{ $log->comment }}</div>
        </div>
        <hr class="my-0" />
    @endforeach
</div>
