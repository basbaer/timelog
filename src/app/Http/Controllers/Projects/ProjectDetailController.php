<?php

namespace App\Http\Controllers\Projects;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Services\ProjectService;
use Carbon\Carbon;
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
        $workingTypes = $project->keys()
            ->reject(fn($key) => $key === 'project')
            ->values()
            ->all();

        $projectForView = collect([
            'title' => $project->get('project')->get('title'),
            'id' => $project->get('project')->get('id'),
            'working_types' => $workingTypes,
        ]);

        foreach ($workingTypes as $workingType) {
            $projectForView->put($workingType, collect([
                'sum' => $project->get($workingType)->get('sum'),
                'logs' => $project->get($workingType)->get('logs'),
            ]));
        }

        return $projectForView;
    }

    public function close(int $id){
        $project = Project::findOrfail($id);

        $project->end_date = Carbon::today();
        
        $project->save();

        return redirect()->route('admin.project.detail', $id);
    }
}
