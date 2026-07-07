<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProjectUpdateRequest;
use App\Models\Project;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Collection;

class AddProjectController extends Controller
{
    public function show()
    {
        // get all roles except admin role
        $roles = Role::worker()->with('users:id,first_name,last_name,role_id')->get();
        $project = null;
        
        return view('admin/projects-add', compact('roles', 'project'));
    }

    public function store(ProjectUpdateRequest $request)
    {
        $validatedData = $request->validated();

        // Create a new project using the validated data
        $project = new Project();
        $project->location = $validatedData['location'];
        $project->date = $validatedData['date'];
        $project->client = $validatedData['client'];
        $project->save();

        $this->syncProjectAssignments(
            $project,
            $validatedData['roles'],
            $validatedData['workers'] ?? []
        );

        // Redirect back to the projects overview page with a success message
        return redirect()->route('admin.projects.overview')->with('success', 'Projekt erfolgreich angelegt!');
    }

    public function edit(int $projectId)
    {
        // Find the project by ID
        $project = Project::findOrFail($projectId);

        // Get all roles except admin role
        $roles = Role::worker()->with('users:id,first_name,last_name,role_id')->get();

        // Infer selected roles from assigned workers.
        $assignedRoleIds = $project->users()
            ->pluck('users.role_id')
            ->map(fn ($roleId) => (int) $roleId)
            ->unique()
            ->values()
            ->all();
        $assignedUserIds = $project->users()->pluck('id')->toArray();

        return view('admin/projects-add', compact('project', 'roles', 'assignedRoleIds', 'assignedUserIds'));
    }

    public function update(ProjectUpdateRequest $request, int $projectId)
    {
        $validatedData = $request->validated();

        // Find the project by ID
        $project = Project::findOrFail($projectId);

        // Update the project with the validated data
        $project->location = $validatedData['location'];
        $project->date = $validatedData['date'];
        $project->client = $validatedData['client'];
        // Every time the project is edit, it is opended again
        $project->end_date = null;
        $project->save();

        $this->syncProjectAssignments(
            $project,
            $validatedData['roles'],
            $validatedData['workers'] ?? []
        );

        // Redirect back to the projects overview page with a success message
        return redirect()->route('admin.project.detail', ['id' => $project->id])->with('success', 'Projekt erfolgreich aktualisiert!');
    }

    private function syncProjectAssignments(Project $project, array $roleIds, array $selectedWorkerIds): void
    {
        $roleIds = array_values(array_unique(array_map('intval', $roleIds)));
        $selectedWorkerIds = array_values(array_unique(array_map('intval', $selectedWorkerIds)));

        /** @var Collection<int, \App\Models\User> $workersInSelectedRoles */
        $workersInSelectedRoles = User::query()
            ->whereIn('role_id', $roleIds)
            ->get(['id', 'role_id']);

        $workersByRole = $workersInSelectedRoles->groupBy('role_id');
        $selectedLookup = array_flip($selectedWorkerIds);

        $resolvedWorkerIds = [];

        foreach ($roleIds as $roleId) {
            $workersOfRole = $workersByRole->get($roleId, collect());

            $selectedWorkersOfRole = $workersOfRole
                ->pluck('id')
                ->filter(fn (int $id) => isset($selectedLookup[$id]))
                ->values()
                ->all();

            if (! empty($selectedWorkersOfRole)) {
                $resolvedWorkerIds = array_merge($resolvedWorkerIds, $selectedWorkersOfRole);
                continue;
            }

            $resolvedWorkerIds = array_merge($resolvedWorkerIds, $workersOfRole->pluck('id')->all());
        }

        $project->users()->sync(array_values(array_unique($resolvedWorkerIds)));
    }
}
