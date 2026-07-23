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

        return view('print/print', [
            'worker_id' => $worker->id,
            'project' => $project,
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
}
