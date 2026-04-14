<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Project;

class OverviewProjectController extends Controller
{
    public function show()
    {
        $projects = Project::OpenProjects()->get();

        return view('admin/projects-overview', compact('projects'));
    }
}
