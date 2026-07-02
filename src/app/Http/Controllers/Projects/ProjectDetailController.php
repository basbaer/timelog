<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Services\ProjectService;
use Illuminate\Support\Collection;

class ProjectDetailController extends Controller
{
    public function show(int $id)
    {
        // Get the project details from the database
        $projectService = new ProjectService();
        $project = $projectService->getDetailedProject($id);

        $projectForView = $this->prepareProjectForView($project);

        return view('admin/projects-detail', ['project' => $projectForView]);
    }

    private function prepareProjectForView(Collection $project): Collection
    {
        $projectForView = collect([
            'title' => $project->get('project')->get('title'),
            'working_types' => $project->keys()->filter(function ($key) {
                return $key !== 'project';
            })->values(),            
            'project' => $project->get('project'),
            'harvester' => $project->get('harvester'),
            'rueckezug' => $project->get('rueckezug'),
        ]);

        return $projectForView;
    }
}
