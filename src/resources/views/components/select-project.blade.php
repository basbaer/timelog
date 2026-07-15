@props([
    'openProjects' => [],
])

<div class="col-2 mx-auto">
    <select class=" form-select" aria-label="Default select example">
        <option selected>{{ __('form.all') }}</option>
        @foreach ($openProjects as $project)
            : ?>
            <option value="{{ $project->id }}">{{ $project->title }}</option>
        @endforeach
    </select>
</div>
