 <div class="mt-3">
        <div class="row">
            <div class="col justify-content-start d-flex">
                <h2 class="h2 m-3">{{ $name }}</h2>
            </div>
            <div class="col justify-content-end d-flex">
                <a href="{{ route('admin.worker.card', ['worker_id' => $worker_id]) }}" type="button"
                    class="btn btn-secondary m-3">Kontaktinformationen</a>
            </div>
        </div>
    </div>

    @if (session('success'))
        <div class="container mt-3">

            <div class="alert alert-success" role="alert">
                {{ session('success') }}
            </div>
        </div>
    @endif

    <div class="container mt-3 d-flex justify-content-center align-items-center gap-4">
        <a href="{{ route('admin.worker.show', ['worker_id' => $worker_id, 'month' => $previousMonth]) }}"
            id="previousMonthBtn" aria-label="Vorheriger Monat">
            <i class="bi bi-caret-left-fill" style="font-size: 1.5rem; color: grey;" aria-hidden="true"></i>
        </a>
        <h3 class="mb-0">{{ $month }}</h3>
        <a href="{{ route('admin.worker.show', ['worker_id' => $worker_id, 'month' => $nextMonth]) }}" 
            id="nextMonthBtn" aria-label="Nächster Monat">
            <i class="bi bi-caret-right-fill" style="font-size: 1.5rem; color: grey;" aria-hidden="true"></i>
        </a>
    </div>
</div>