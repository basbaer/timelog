@props(['logs'])

<div class="container">
    <div class="row mt-2">
        <div class="col-2"><strong>Datum</strong></div>
        <div class="col-2"><strong>Stunden</strong></div>
        <div class="col-2"><strong>Arbeiter</strong></div>
        <div class="col-6"><strong>Kommentar</strong></div>
        <hr class="my-0" />
    </div>
    @foreach ($logs as $log)
        <div class="row">
            <div class="col-2">{{ $log->date }}</div>
            <div class="col-2">{{ $log->sum }}</div>
            <div class="col-2">{{ $log->user->first_name }} {{ $log->user->last_name }}</div>
            <div class="col-6">{{ $log->comment }}</div>
        </div>
        <hr class="my-0" />
    @endforeach
</div>
