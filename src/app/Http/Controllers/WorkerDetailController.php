<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\WorkerLogService;
use App\Services\ProjectService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;

class WorkerDetailController extends Controller
{
    public function __construct(
        private readonly WorkerLogService $workerLogService,
        private readonly ProjectService $projectService
    ) {}

    public function show(int $worker_id)
    {
        try {
            // Get worker details from the database using the $id
            $worker = User::findOrFail($worker_id);

            $requestedMonth = request()->query('month');
            $currentMonth = Carbon::now()->startOfMonth();

            if ($requestedMonth) {
                try {
                    $currentMonth = Carbon::createFromFormat('Y-m', $requestedMonth)->startOfMonth();
                } catch (\Exception $e) {
                    $currentMonth = Carbon::now()->startOfMonth();
                }
            }

            $first_of_current_month = $currentMonth->copy()->startOfMonth()->toDateString();
            $last_of_current_month = $currentMonth->copy()->endOfMonth()->toDateString();
            $logEntries = $this->workerLogService->getLogsFor(
                $worker,
                $first_of_current_month,
                $last_of_current_month
            );

            $month = $currentMonth->translatedFormat('F Y');
            $previousMonth = $currentMonth->copy()->subMonth()->format('Y-m');
            $nextMonth = $currentMonth->copy()->addMonth()->format('Y-m');

            $openProjects = $this->projectService->getOpenProjects($worker->id, $first_of_current_month, $last_of_current_month);
            $selectedProject = request()->query('project', 'all');


            if ($selectedProject !== 'all') {
                // Get the log entries only for the specific project and worker
                $logEntries = $this->workerLogService->getLogsFor($worker, $first_of_current_month, $last_of_current_month, (int) $selectedProject);
            } else {
                // Get all log entries for this worker
                $logEntries = $this->workerLogService->getLogsFor($worker, $first_of_current_month, $last_of_current_month);
            }


            return view('admin/workers-detail', [
                'name' => $worker->first_name . ' ' . $worker->last_name,
                'worker_id' => $worker->id,
                'role' => $worker->role->slug,
                'logEntries' => $logEntries,
                'selectedProject' => $selectedProject,
                'openProjects' => $openProjects,
                'month' => $month,
                'previousMonth' => $previousMonth,
                'nextMonth' => $nextMonth,
            ]);
        } catch (ModelNotFoundException $e) {
            // Handle the case where the worker is not found
            return redirect()->route('admin.workers.overview')->with('error', 'Worker not found.');
        }
    }

    public function addWorkLog(int $worker_id)
    {
        try {
            // Get worker details from the database using the $id
            $worker = User::findOrFail($worker_id);
            $role = $worker->role?->slug;

            if ($role === 'forstwirt') {
                return redirect()->route('log.forstwirt', ['user_id' => $worker_id]);
            } elseif ($role === 'harvester') {
                return redirect()->route('log.harvester', ['user_id' => $worker_id]);
            } elseif ($role === 'rueckezug') {
                return redirect()->route('log.rueckezug', ['user_id' => $worker_id]);
            } else {
                return redirect()->route('admin.workers.overview')->with('error', 'Invalid worker role.');
            }
        } catch (ModelNotFoundException $e) {
            // Handle the case where the worker is not found
            return redirect()->route('admin.workers.overview')->with('error', 'Worker not found.');
        }
    }
}
