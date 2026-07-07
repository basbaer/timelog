<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\Project;

class OverviewProjectController extends Controller
{
    public function show()
    {
        $projects = Project::OpenProjects()->get();
        $closed = false;

        return view('admin/projects-overview', compact('projects', 'closed'));
    }

    public function showClosed()
    {
        $projects = Project::ClosedProjects()->get();
        $closed = true;

        return view('admin/projects-overview', compact('projects', 'closed'));
    }
}
