@props([
    'openProjects' => [],
    'selectedProject' => 'all',
])

<div class="col-2 mx-auto">
    <select class="form-select" aria-label="Projekt filtern" onchange="filterByProject(this.value)">
        <option value="all" @selected($selectedProject === 'all')>{{ __('form.all') }}</option>
        @foreach ($openProjects as $project)
            <option value="{{ $project->id }}" @selected((string) $selectedProject === (string) $project->id)>
                {{ $project->title }}
            </option>
        @endforeach
    </select>
</div>

<script>
    function filterByProject(projectId) {
        const url = new URL(window.location.href);
        if (projectId === 'all') {
            url.searchParams.delete('project');
        } else {
            url.searchParams.set('project', projectId);
        }
        window.location.href = url.toString();
    }
</script>