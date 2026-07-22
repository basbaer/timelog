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
        
        $projects = $this->projectService->getOpenProjects($worker_id, now()->startOfMonth()->toDateString(), now()->endOfMonth()->toDateString());

        $hasClosedProjects = $this->projectService->hasClosedProjects($worker_id);

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
}
