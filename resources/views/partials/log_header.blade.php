<div class="container d-flex justify-content-between align-items-center">
    <h1 class="mt-2">{{ $name }}</h1>
    <!-- Logout Button -->
    <div class="d-flex justify-content-end">
        <a href="#" class="btn btn-outline-secondary me-2" aria-label="Settings">
            <i class="bi bi-gear"></i>
        </a>

        <a href="{{ route('worker.show', ['worker_id' => $worker_id]) }}"
            class="btn btn-primary me-2">{{ __('form.overview_hours') }}</a>
        <a href="/logout" class="btn btn-outline-danger">Logout</a>
    </div>
</div>
