<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Controller;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Services\WorkerLogService;

abstract class BaseLogController extends Controller
{
    protected WorkerLogService $workerLogService;

    public function __construct(WorkerLogService $workerLogService)
    {
        $this->workerLogService = $workerLogService;
    }
    // Subklassen müssen diese liefern:
    abstract protected function logModel(): string;       // z.B. ForstwirtLog::class
    abstract protected function workingTypeModel(): string;
    abstract protected function route(): string;    // z.B. 'log.forstwirt' oder 'log.harvester'
    abstract protected function viewPrefix(): string;     // z.B. 'log-forstwirt'
    abstract protected function mapValidatedToLogs(array $validated): array;
    abstract protected function addPreviousData(int $user_id, Collection $projects): Collection;

    private function getUserAndProjects(?int $user_id): array
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $isAdmin = false;

        // If an id is provided and the authenticated user is an admin, load the specified user.
        // Permission to view other users' logs is enforced in middleware.
        if ($user->isAdmin() && $user_id !== null) {
            $user = User::findOrFail($user_id);
            $isAdmin = true;
        }

        $user_id = $user->id;
        $name = $user->first_name . ' ' . $user->last_name;
        // Get all open projects for the user's role
        $projects = $user->openProjects()->get();

        // Change $projects keys to id's
        $projects = $projects->keyBy('id');

        $projects = $this->addPreviousData($user_id, $projects);

        return compact(['isAdmin', 'user', 'name', 'projects', 'user_id']);
    }

    /**
     * Common logic for showing the log form
     * 
     * Note: $id is optional and only used when an admin wants to view the log form for a specific user.
     *       Regular users will not provide an id and will see their own log form (when they try to add an id, the middelware handels the restriction).
     */
    public function show($user_id = null)
    {
        // After this call, $user is definetly set
        $data = $this->getUserAndProjects($user_id);
        extract($data); // Extrahiere Variablen wie $isAdmin, $user, $name, $projects, $user_id

        // Check if today is alreay logged
        $today = now()->toDateString();
        $logClass = $this->logModel();
        // Just to check if there are any logs for today, the actual log data is loaded in the success method
        $existingLog = $this->getLogOfToday($user_id);
        // if there is an existing log, show the success page instead of log form - but only for non-admin users (admins can view the log form for any user, even if they already have a log for today)
        if ($existingLog && !$isAdmin) {
            // Route like: log.forstwirt.success
            session()->flash('last_log', $existingLog); // Store log in session for retrieval in success method
            return redirect()->route($this->route() . '.success', ['worker_id' => (int) $user_id]);
        }

        $viewPrefix = $this->viewPrefix();
        // Route like: log-forms/log-forstwirt
        return view('log-forms/' . $viewPrefix, compact(['projects', 'isAdmin', 'name', 'user_id']));
    }

    /**
     * Common logic for showing succes page
     * 
     * getLogOfToday() is used to retrieve any log from that day,
     * just to see if there are any logs. 
     * The actual logs shown on the success page are loaded in buildSuccessOverview, 
     * which is called inside the success method of each controller.
     * 
     */
    public function success(int $worker_id)
    {
        $log = session()->get('last_log') ?: $this->getLogOfToday($worker_id);

        if (!$log) {
            return redirect()->route($this->route())->with('error', 'Log entry not found.');
        }

        $log_id = $log->id;

        $log = $this->logModel()::with(['project'])->findOrFail($log_id);
        $user_id = $log->user_id;
        $log_user = User::findOrFail($user_id);
        $name = $log_user->first_name . ' ' . $log_user->last_name;
        $logDate = Carbon::parse($log->date);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isAdmin()) {
            // Admin is redirected to worker detail page
            return redirect()->route('admin.worker.show', ['id' => $user_id])->with('success', 'Eintrag erfolgreich hinzugefügt.');
        } else {
            $logOverview = $this->buildSuccessOverview($user_id, $logDate);
            $deleteRouteName = $this->route() . '.delete';

            $logDate = Carbon::parse($log->date)->format('d.m.Y');
            return view('log-forms/log-success', compact('name', 'user_id', 'log_id', 'logOverview', 'logDate', 'deleteRouteName'));
        }
    }

    protected function buildSuccessOverview(int $userId, string $date): Collection
    {
        return $this->loadSuccessLogs($userId, $date)
            ->groupBy(fn($log) => $log->project_id)
            ->map(function (Collection $logs) {
                $firstLog = $logs->first();

                return [
                    'project' => $firstLog->project,
                    'logs' => $logs->values(),
                ];
            })
            ->values();
    }

    public function deleteLog(int $worker_id)
    {
        $date = session()->get('delete_log_date');
        
        if (!$date) {
            $date = Carbon::today()->toDateString();
        }

        $this->deleteLogsOfDate($worker_id, $date);

        return redirect()->route($this->route())->with('success', 'Eintrag erfolgreich gelöscht.');

    }

    protected function loadSuccessLogs(int $userId, string $date): Collection
    {
        return $this->workerLogService->loadSuccessLogs($userId, $date);
    }

    public function getLogOfToday(int $user_id)
    {
        return $this->workerLogService->getLogOfToday($user_id);
    }

    public function deleteLogsOfDate(int $user_id, string $date)
    {
        $this->workerLogService->deleteLogsFrom($user_id, $date);
    }
}
