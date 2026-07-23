<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Services\WorkerLogService;
use App\Services\ProjectService;

class PrintController extends Controller
{
    public function __construct(
        private readonly WorkerLogService $workerLogService,
        private readonly ProjectService $projectService
    ) {}

    public function preparePrint(int $worker_id)
    {
        $worker = User::findOrFail($worker_id);
        
        // loads all the project that were open at some point in the current month
        $projects = $this->projectService->getOpenProjects($worker_id, now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString());

        // check if the worker has any closed projects before the current month
        $hasClosedProjects = $this->projectService->hasClosedProjects($worker_id, null, now()->startOfMonth()->toDateString());

        return view('print/preparePrintForm', [
            'worker' => $worker,
            'projects' => $projects,
            'hasClosedProjects' => $hasClosedProjects,
        ]);
    }

    public function loadPrint(Request $request, int $worker_id)
    {
        $worker = User::findOrFail($worker_id);
        $project = $request->input('project');
        $timeframe = $request->input('timeframe');
        $month = $request->input('month');
        $role = $worker->role->slug;
        $workTypeForstwirt = $request->input('work-type-forstwirt');
        $workTypeOther = $request->input('work-type-' . $role);

        return redirect()->route('print.show', ['worker_id' => $worker->id, 
        'project' => $project, 
        'timeframe' => $timeframe, 
        'month' => $month,
        'work-type-forstwirt' => $workTypeForstwirt,
        'work-type-' . $role => $workTypeOther
    ]);

        
    }

    public function loadClosedProjects(Request $request, int $worker_id)
    {
        $worker = User::findOrFail($worker_id);
        
        // loads all the closed projects for the worker
        $closedProjects = $this->projectService->getClosedProjects($worker->id);

        return response()->json([
            'closedProjects' => $closedProjects,
        ]);
    }

    public function print(int $worker_id){
        
        
        $worker = User::findOrFail($worker_id);
        $projectId = request()->query('project');

        return view('print/print', [
            'worker' => $worker,
        ]);
    }
}
