<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreForstwirtLogRequest;
use App\Http\Requests\StoreRueckezugLogRequest;
use App\Http\Requests\StoreHarvesterLogRequest;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use App\Services\WorkerLogService;
use App\Services\ProjectService;
use Illuminate\Http\JsonResponse;

abstract class BaseLogController extends Controller
{
    protected WorkerLogService $workerLogService;
    protected ProjectService $projectService;

    public function __construct(WorkerLogService $workerLogService, ProjectService $projectService)
    {
        $this->workerLogService = $workerLogService;
        $this->projectService = $projectService;
    }
    // Subklassen müssen diese liefern:
    abstract protected function logModel(): string;       // z.B. ForstwirtLog::class
    abstract protected function logService(): string;     // z.B. ForstwirtLogService::class
    abstract protected function route(): string;    // z.B. 'log.forstwirt' oder 'log.harvester'
    abstract protected function viewPrefix(): string;     // z.B. 'log-forstwirt'
    abstract protected function addPreviousData(int $user_id, Collection $projects): Collection;
    abstract protected function buildEditPrefill(Collection $logs, string $date): array;


    private function getWorker(?int $worker_id): array
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $isAdmin = false;

        // If an id is provided and the authenticated user is an admin, load the specified user.
        // Permission to view other users' logs is enforced in middleware.
        if ($user->isAdmin() && $worker_id !== null) {
            $worker = User::findOrFail($worker_id);
            $isAdmin = true;
        }else{
            $worker = $user;
        }

        $worker->full_name = $worker->first_name . ' ' . $worker->last_name;
        $worker->type = $worker->role->slug; // e.g. 'forstwirt' or 'harvester'

        return [$worker, $isAdmin];
    }

    private function getProjects(User $worker): Collection
    {
        // Get all open projects for the user's role
        $projects = $worker->openActiveProjects()->get();

        // Change $projects keys to id's
        $projects = $projects->keyBy('id');

        $projects = $this->addPreviousData($worker->id, $projects);

        return $projects;
    }

    /**
     * Common logic for showing the log form
     * 
     * Note: $id is optional and only used when an admin wants to view the log form for a specific user.
     *       Regular users will not provide an id and will see their own log form (when they try to add an id, the middelware handels the restriction).
     */
    public function show(?int $worker_id = null)
    {
        [$worker, $isAdmin] = $this->getWorker($worker_id);

        $projects = $this->getProjects($worker);

        /*
        $editLogId = request()->integer('edit_log_id');
        $editingLogId = null;
        $editingProjectId = null;
        $editingLogDate = null;
        $prefill = [];

        if ($editLogId) {
            $logClass = $this->logModel();
            $editLog = $logClass::with(['project', 'user.role'])
                ->where('user_id', $user_id)
                ->findOrFail($editLogId);

            $editingLogDate = Carbon::parse($editLog->date)->toDateString();
            $editLogs = $this->workerLogService->loadSuccessLogs($user_id, $editingLogDate);

            $prefill = $this->buildEditPrefill($editLogs, $editingLogDate);
            $editingLogId = $editLog->id;
            $editingProjectId = $editLog->project_id;
        }
*/
        // Check if today is alreay logged
        $today = now()->toDateString();
        //query date param
        $date = request()->query('date', $today);

        $existingLogs = $this->workerLogService->loadLogs($worker, $date);
        
        // if there is an existing log, show the success page instead of log form - but only for non-admin users (admins can view the log form for any user, even if they already have a log for today)
        /*if ($existingLog && !$isAdmin) {
            // Route like: log.forstwirt.success
            session()->flash('last_log', $existingLog); // Store log in session for retrieval in success method
            return redirect()->route($this->route() . '.success', ['worker_id' => (int) $user_id]);
        }
*/
        return view('log-forms/log', compact(['date', 'projects', 'worker', 'existingLogs', 'isAdmin']));      
        //return view('log-forms/log', compact(['projects', 'worker', 'existingLogs', 'prefill', 'editingLogId', 'editingProjectId', 'editingLogDate']));
    }

    public function storeLog(StoreForstwirtLogRequest|StoreRueckezugLogRequest|StoreHarvesterLogRequest $request): JsonResponse
    {
        $validated = $request->validated();
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $workerId = (int) $validated['worker_id'];

        // prevent form spoofing: only allow admins to log for other users
        if (! $user->isAdmin() && $user->id !== $workerId) {
            return response()->json(['error' => 'Ungültige Benutzer-ID.'], 422);
        }

        $editLogId = $validated['edit_log_id'] ?? null;

        if ($editLogId) {
            $originalDate = $validated['edit_log_date'] ?? $validated['log_date'];
            $this->workerLogService->deleteLogsFrom($workerId, $originalDate);
        }

        $log = $this->workerLogService->saveLog($validated);

        $log->projectTitle = $log->project->title;
        $json = response()->json([
            'success' => true,
            'html' => view('log-forms.partials.log-summary-item', ['savedLog' => $log])->render(),
        ]);
    
        return $json;
    }

    /*
    protected function buildSuccessOverview(int $userId, string $date): Collection
    {
        return $this->workerLogService->loadLogs($userId, $date)
            ->groupBy(fn($log) => $log->project_id)
            ->map(function (Collection $logs) {
                $firstLog = $logs->first();

                $totalStart = $logs->min(fn($log) => strtotime($log->start));
                $totalStart = date("H:i", $totalStart);

                $totalEnd = $logs->max(fn($log) => strtotime($log->end));
                $totalEnd = date("H:i", $totalEnd);

                $totalSum = $logs->sum(fn($log) => $log->sum ? strtotime($log->sum) : 0);
                $totalSum = date("H:i", $totalSum);

                return [
                    'project' => $firstLog->project,
                    'logs' => $logs->values(),

                    //Add total start, end and sum for each project
                    'totalStart' => $totalStart,
                    'totalEnd' => $totalEnd,
                    'totalSum' => $totalSum,
                ];
            })
            ->values();

    }
            */

    public function deleteLog(Request $request, int $worker_id)
    {
        if ($request->filled('delete_log_date')) {
            session()->put('delete_log_date', $request->input('delete_log_date'));
        }

        $date = session()->get('delete_log_date', Carbon::today()->toDateString());

        $this->deleteLogsOfDate($worker_id, $date);

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if ($user->isAdmin()) {
            return redirect()->route('admin.worker.show', ['worker_id' => $worker_id])
                ->with('success', 'Eintrag erfolgreich gelöscht.');
        }

        return redirect()->route($this->route())->with('success', 'Eintrag erfolgreich gelöscht.');
    }

    public function getLogOfToday(int $user_id)
    {
        return $this->workerLogService->getLogOfToday($user_id);
    }

    public function deleteLogsOfDate(int $user_id, string $date)
    {
        $this->workerLogService->deleteLogsFrom($user_id, $date);
    }

    protected function getSumForMainLog(array $workLog): ?string
    {
        if (isset($workLog['sum'])) {
            $total = $workLog['sum'];
        } else {
            return null;
        }

        $forstwirtSum = date("H:i", strtotime("00:00"));

        if (isset($workLog['entries']) && is_array($workLog['entries'])) {
            foreach ($workLog['entries'] as $entry) {
                if (isset($entry['sum'])) {
                    $forstwirtSum = strtotime($forstwirtSum) + strtotime($entry['sum']);
                    $forstwirtSum = date("H:i", $forstwirtSum);
                }
            }
        }

        $total = strtotime($total) - strtotime($forstwirtSum);
        $total = date("H:i", $total);

        return $total;
    }
}
