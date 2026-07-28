<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Services\WorkerLogService;
use App\Services\ProjectService;
use Illuminate\Support\Collection;

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
        $workType = $request->input('work-type');
        if (!$workType) {
            $workType = 'forstwirt'; // Default to 'forstwirt' if no work type is selected
        }

        return redirect()->route('print.show', [
            'worker_id' => $worker->id,
            'project' => $project,
            'timeframe' => $timeframe,
            'month' => $month,
            'work-type' => $workType,
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

    public function print(int $worker_id)
    {

        $worker = User::findOrFail($worker_id);

        $project = request()->query('project');
        $projectId = $this->getProjectId($project);
        $project = $projectId ? $this->projectService->getProjectById($projectId) : 'all';

        $timeframe = request()->query('timeframe');
        $month = request()->query('month');
        $fromDate = $this->getFromDate($timeframe, $month);
        $toDate = $this->getToDate($timeframe, $month);
        // Convert month into format "mm/yy" for display
        if ($timeframe === 'month' && $month) {
            $monthYear = explode('-', $month);
            $year = $monthYear[0];
            $month = $monthYear[1];
            $month = sprintf('%02d/%02d', $month, $year % 100); // Format as "mm/yy"
        }

        $role = $worker->role->slug;
        $workType = request()->query('work-type');


        $logTypes = collect([$workType]);

        $logs = $this->workerLogService->getLogsFor($worker, $fromDate, $toDate, $projectId)
            ->filter(function ($log) use ($logTypes) {
                return $logTypes->contains($log->entry_label);
            });

        $tableHeaders = $this->getTableHeaders($workType, $projectId);

        return view('print/print', [
            'worker' => $worker,
            'project' => $project,
            'logs' => $logs,
            'logType' => $workType,
            'tableHeaders' => $tableHeaders,
            'timeframe' => $timeframe,
            'month' => $month,
        ]);
    }

    private function getProjectId(string $project): ?int
    {
        if (!is_numeric($project)) {
            $projectId = null; // Set to null to indicate all projects
        } else {
            $projectId = (int) $project;
        }
        return $projectId;
    }

    private function getFromDate(string $timeframe, ?string $month): ?string
    {
        if ($timeframe === 'month' && $month) {
            //Extract month and year from month string (format: mm/yy)
            $monthYear = explode('-', $month);
            $year = (int) $monthYear[0];
            $month = (int) $monthYear[1];
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
            $monthYear = explode('-', $month);
            $year = (int) $monthYear[0];
            $month = (int) $monthYear[1];
            return now()->year($year)->month($month)->endOfMonth()->toDateString();
        } elseif ($timeframe === 'whole') {
            return now()->endOfMonth()->toDateString(); // Arbitrary late date for "whole" timeframe
        }
        return null;
    }

    private function getTableHeaders(string $logType, ?int $projectId): Collection
    {
        $headers = collect();

        $headers->put($logType, collect($this->workerLogService->getPrintTableHeadersFor($logType)));

        if ($projectId !== null) {
            // Remove the 'title' header if a specific project is selected
            $headers->get($logType)->forget('title');
        }

        return $headers;
    }
}
