<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Controller;
use App\Models\ForstwirtLog;
use App\Models\ForstwirtWorkingType;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

abstract class BaseLogController extends Controller
{
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
     *       Regular users will not provide an id and will see their own log form.
     */
    public function show($user_id = null) { 
 
        $data = $this->getUserAndProjects($user_id);
        extract($data); // Extrahiere Variablen wie $isAdmin, $user, $name, $projects, $user_id

        // Check if today is alreay logged
        $today = now()->toDateString();
        $logClass = $this->logModel();
        $existingLog = $logClass::where('user_id', $user_id)
            ->where('date', $today)
            ->first();
        // if there is an existing log, show the success page instead of log form - but only for non-admin users (admins can view the log form for any user, even if they already have a log for today)
        if ($existingLog && !$isAdmin) {
            // Route like: log.forstwirt.success
            return redirect()->route($this->route() . '.success', ['log_id' => $existingLog->id]);
        }

        $viewPrefix = $this->viewPrefix();
        // Route like: log-forms/log-forstwirt
        return view('log-forms/' . $viewPrefix, compact(['projects', 'isAdmin', 'name', 'user_id']));
    }

    public function success(int $log_id) {
        $user_id = $this->logModel()::findOrFail($log_id)->user_id;
        $log_user = User::findOrFail($user_id);
        $name = $log_user->first_name . ' ' . $log_user->last_name;
        $log = $this->logModel()::findOrFail($log_id);

        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user->isAdmin()) {
            // Admin is redirected to worker detail page
            return redirect()->route('admin.worker.show', ['id' => $user_id])->with('success', 'Eintrag erfolgreich hinzugefügt.');
        } else if ($log->user_id !== $user->id) {
            //Check if the log belongs to the authenticated user
            return redirect()->route($this->route())->with('error', 'Unauthorized access to log entry.');
        } else {
            return view('log-forms/log-success', compact('name', 'log_id'));
        }
    }
    public function deleteLog(int $log_id) {
        $log = $this->logModel()::findOrFail($log_id);

        // Check if the log belongs to the authenticated user
        if ($log->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        // Delete the log and its entries
        $log->entries()->delete();
        $log->delete();
        return redirect()->route($this->route())->with('success', 'Log entry deleted successfully.');
    }

    /**
     * Persist entries in the same shape used by Forstwirt logs.
     */
    protected function saveForstwirtLogs(array $mappedLogs, int $userId): ?ForstwirtLog
    {
        $lastLog = null;

        foreach ($mappedLogs as $logData) {
            $log = new ForstwirtLog();
            $log->user_id = $userId;
            $log->project_id = $logData['project_id'];
            $log->working_type_id = ForstwirtWorkingType::where('slug', $logData['type'])->value('id');
            $log->date = $logData['date'];
            $log->start = $logData['start'];
            $log->end = $logData['end'];
            $log->pause = $logData['pause'] ?? 0;
            $log->sum = $logData['sum'] ?? null;
            $log->comment = $logData['comment'] ?? null;
            $log->save();

            $lastLog = $log;
        }

        return $lastLog;
    }
}

?>