<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;

class ProjectDetailController extends Controller
{
    public function show(int $id)
    {
        return view('admin/projects-detail', ['projectId' => $id]);
    }
}
