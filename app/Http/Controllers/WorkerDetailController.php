<?php

namespace App\Http\Controllers;

use App\Models\ForstwirtLog;
use App\Models\HarvesterLog;
use App\Models\RueckezugLog;
use App\Models\User;
use App\Services\WorkerLogService;
use App\Services\ProjectService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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

             /** @var \App\Models\User $user */
            $user = Auth::user();
            $isAdmin = $user->isAdmin();
            $view = $isAdmin ? 'admin/workers-detail' : 'workers-detail';

            return view($view, [
                'name' => $worker->first_name . ' ' . $worker->last_name,
                'worker_id' => $worker->id,
                'role' => $worker->role->slug,
                'logEntries' => $logEntries,
                'selectedProject' => $selectedProject,
                'openProjects' => $openProjects,
                'month' => $month,
                'previousMonth' => $previousMonth,
                'nextMonth' => $nextMonth,
                'isAdmin' => $isAdmin,
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
                return redirect()->route('log.forstwirt', ['worker_id' => $worker_id]);
            } elseif ($role === 'harvester') {
                return redirect()->route('log.harvester', ['worker_id' => $worker_id]);
            } elseif ($role === 'rueckezug') {
                return redirect()->route('log.rueckezug', ['worker_id' => $worker_id]);
            } else {
                return redirect()->route('admin.workers.overview')->with('error', 'Invalid worker role.');
            }
        } catch (ModelNotFoundException $e) {
            // Handle the case where the worker is not found
            return redirect()->route('admin.workers.overview')->with('error', 'Worker not found.');
        }
    }

    public function print(int $worker_id, string $project)
    {
        $worker = User::findOrFail($worker_id);
        if ($project !== 'all') {
            $logEntries = $this->workerLogService->getLogsForProject($worker_id, $project);
        }else{
            $logEntries = $this->workerLogService->getLogsFor($worker_id);
        }

        // Get project name for the selected project
        if ($project !== 'all') {
            $project = $this->projectService->getProjectById($project);
        }

        return view('admin/workers-detail-print', [
            'worker_id' => $worker_id,
            'name' => $worker->first_name . ' ' . $worker->last_name,
            'role' => $worker->role?->slug,
            'project' => $project,
            'logEntries' => $logEntries,
        ]);
    }

    public function deleteLog(Request $request, int $worker_id, int $log_id)
    {
        $slug = $request->input('delete_type');
        try {
            $this->workerLogService->deleteLog($log_id, $slug);
            return redirect()->route('admin.worker.show', ['worker_id' => $worker_id])->with('success', 'Log entry deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->route('admin.worker.show', ['worker_id' => $worker_id])->with('error', 'Failed to delete log entry.');
        }
    }

    public function editLog(Request $request, int $worker_id, int $log_id)
    {
        try {
            $worker = User::findOrFail($worker_id);

            $logType = $request->input('log_type');

            $clickedLog = match ($logType) {
                'harvester' => HarvesterLog::find($log_id),
                'rueckezug' => RueckezugLog::find($log_id),
                'forstwirt' => ForstwirtLog::find($log_id),
                default => HarvesterLog::find($log_id)
                    ?? RueckezugLog::find($log_id)
                    ?? ForstwirtLog::find($log_id),
            };

            if (! $clickedLog || (int) $clickedLog->user_id !== (int) $worker->id) {
                return redirect()->route('admin.worker.show', ['worker_id' => $worker_id])
                    ->with('error', 'Log entry not found.');
            }

            $routeName = match (class_basename($clickedLog)) {
                'HarvesterLog' => 'log.harvester.edit',
                'RueckezugLog' => 'log.rueckezug.edit',
                default => 'log.forstwirt.edit',
            };

            return redirect()->route($routeName, [
                'worker_id' => $worker_id,
                'edit_log_id' => $clickedLog->id,
                'log_id' => $clickedLog->id,
            ]);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('admin.workers.overview')->with('error', 'Worker not found.');
        }
    }
}
