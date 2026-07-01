<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Services\ProjectService;

class ProjectDetailController extends Controller
{
    public function show(int $id)
    {
        // Get the project details from the database
        $projectService = new ProjectService();
        $project = $projectService->getDetailedProject($id);

        return view('admin/projects-detail', ['project' => $project]);
    }
}
