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

        $project = request()->query('project');
        $projectId = $this->getProjectId($project);

        $timeframe = request()->query('timeframe');
        $month = request()->query('month');
        $fromDate = $this->getFromDate($timeframe, $month);
        $toDate = $this->getToDate($timeframe, $month);
        

        $role = $worker->role->slug;
        $workTypeForstwirt = request()->query('work-type-forstwirt');
        $workTypeOther = request()->query('work-type-' . $role);

        $logTypes = collect();
        if ($role === 'forstwirt' || $workTypeForstwirt) {
            $logTypes->push('forstwirt');
        }

        if ($workTypeOther) {
            $logTypes->push($role);
        }

        $logs = $this->workerLogService->getLogsFor($worker, $fromDate, $toDate, $projectId)
            ->filter(function ($log) use ($logTypes) {
                return $logTypes->contains($log->entry_label);
            });

        $hasMultipleLogTypes = false;
        //check if the collection has more than one entry
        if ($logTypes->count() > 1) {
            $hasMultipleLogTypes = true;
        }

        return view('print/print', [
            'worker' => $worker,
            'project' => $project,
            'logEntries' => $logs,
            'hasMultipleLogTypes' => $hasMultipleLogTypes,
            'timeframe' => $timeframe,
        ]);
    }

    private function getProjectId(string $project): ?int
    {
        if (!is_numeric($project)) {
            $projectId = null; // Set to null to indicate all projects
        }else{
            $projectId = (int) $project;
        }
        return $projectId;
    }

    private function getFromDate(string $timeframe, ?string $month): ?string
    {
        if ($timeframe === 'month' && $month) {
            //Extract month and year from month string (format: mm/yy)
            $monthYear = explode('/', $month);
            $month = (int) $monthYear[0];
            $year = (int) ("20" . $monthYear[1]);
            return now()->year($year)->month($month)->startOfMonth()->toDateString();
        } elseif ($timeframe === 'whole') {
            return now()->year(2000)->month(1)->startOfMonth()->toDateString(); // Arbitrary early date for "whole" timeframe
        }
        return null;
    }

    private function getToDate(string $timeframe, ?string $month): ?string
    {
        if ($timeframe === 'month' && $month) {
            //Extract month and year from month string (format: mm/yy)
            $monthYear = explode('/', $month);
            $month = (int) $monthYear[0];
            $year = (int) ("20" . $monthYear[1]);
            return now()->year($year)->month($month)->endOfMonth()->toDateString();
        } elseif ($timeframe === 'whole') {
            return now()->endOfMonth()->toDateString(); // Arbitrary late date for "whole" timeframe
        }
        return null;
    }
}

