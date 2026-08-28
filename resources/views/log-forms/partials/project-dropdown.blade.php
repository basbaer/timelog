<div class="row">
    <div class="col-10 col-md-5 mb-3">
        @php($selectedProjectId = old('project_id', data_get($prefill, 'project_id')))
        <select id="project_id" name="project_id" class="form-select" required>
            <option value="" {{ $selectedProjectId ? '' : 'selected' }} disabled>
                {{ __('form.select_project') }}</option>
            @foreach ($projects as $project)
                <option value="{{ $project->id }}"
                    {{ (string) $selectedProjectId === (string) $project->id ? 'selected' : '' }}>
                    {{ $project->location }} | {{ $project->date->format('m/Y') }} | {{ $project->client }}
                </option>
            @endforeach
        </select>
    </div>
</div>
