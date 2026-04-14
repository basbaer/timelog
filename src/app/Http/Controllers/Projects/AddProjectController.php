<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;
use App\Models\Role;

class AddProjectController extends Controller
{
    public function show()
    {
        // get all roles except admin role
        $roles = Role::worker()->get();
        return view('admin/projects-add', compact('roles'));
    }
    public function store(Request $request)
    {
        // Validate the incoming request data
        $validatedData = $request->validate([
            'location' => 'required|string|max:255',
            'date' => 'required|date',
            'client' => 'required|string|max:255',
            'roles' => 'required|array|min:1',
            'roles.*' => 'integer|exists:roles,id',
        ]);

        // Create a new project using the validated data
        $project = new Project();
        $project->location = $validatedData['location'];
        $project->date = $validatedData['date'];
        $project->client = $validatedData['client'];
        $project->save();

        // Attach selected roles to the project.
        $project->roles()->sync($validatedData['roles']);

        // Redirect back to the projects overview page with a success message
        return redirect()->route('admin.projects.overview')->with('success', 'Projekt erfolgreich angelegt!');
    }
}
