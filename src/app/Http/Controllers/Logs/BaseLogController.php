<?php

namespace App\Http\Controllers\Logs;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

abstract class BaseLogController extends Controller
{
    // Subklassen müssen diese liefern:
    abstract protected function logModel(): string;       // z.B. ForstwirtLog::class
    abstract protected function logEntryModel(): string;  // z.B. ForstwirtLogEntry::class
    abstract protected function workingTypeModel(): string;
    abstract protected function route(): string;    // z.B. 'log.forstwirt' oder 'log.harvester'
    abstract protected function viewPrefix(): string;     // z.B. 'log-forstwirt'
    abstract protected function validationRules(): array;
    abstract protected function filterRequest(Request $request): array;
    abstract protected function mapValidatedToLogs(array $validated): array;
    abstract protected function store(Request $request);


    private function getUserAndProjects(?int $id): array
    {
        $isAdmin = false; 
        if ($this->hasPermissionToViewLog($id)){
            /** @var \App\Models\User $user */
            $user = Auth::user();

            // If the current user is an admin an id is provided to get the specified user
            if ($user->isAdmin() && $id !== null) {
                $user = User::findOrFail($id);
                $isAdmin = true;
            }

        } else {
            abort(403, 'Unauthorized action.');
        }

        $name = $user->first_name . ' ' . $user->last_name;
        // Get all open projects for the user's role
        $projects = $user->openProjects()->get();

        return compact(['isAdmin', 'user', 'name', 'projects', 'id']);
    }

    /**
     * Check if the authenticated user has permission to view the log of the given user ID.
     * Admins can view all logs, while regular users can only view their own logs.
     */
    private function hasPermissionToViewLog($userId): bool
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        return $user->isAdmin() || $user->id === $userId;
    }

    /**
     * Common logic for showing the log form
     * 
     * Note: $id is optional and only used when an admin wants to view the log form for a specific user.
     *       Regular users will not provide an id and will see their own log form.
     */
    public function show($id = null) { 
        
        $data = $this->getUserAndProjects($id);
        extract($data); // Extrahiere Variablen wie $isAdmin, $user, $name, $projects, $id

        // Check if today is alreay logged
        $today = now()->toDateString();
        $logClass = $this->logModel();
        $existingLog = $logClass::where('user_id', $id)
            ->where('date', $today)
            ->first();
        // if there is an existing log, show the success page instead of log form - but only for non-admin users (admins can view the log form for any user, even if they already have a log for today)
        if ($existingLog && !$isAdmin) {
            // Route like: log.forstwirt.success
            return redirect()->route($this->route() . '.success', ['log_id' => $existingLog->id]);
        }

        $viewPrefix = $this->viewPrefix();
        // Route like: log-forms/log-forstwirt
        return view('log-forms/' . $viewPrefix, compact(['projects', 'isAdmin', 'name', 'id']));
    }

    public function validateForm(Request $request): array
    {
        $workLogs = $this->filterRequest($request);

        $request->merge(['work_logs' => $workLogs]);

        $validator = Validator::make($request->all(), $this->validationRules());

        // Ensure that each work type is only selected once per project
        $validator->after(function ($validator) use ($request) {
            foreach ((array) $request->input('work_logs', []) as $projectIndex => $workLog) {
                $types = collect($workLog['entries'] ?? [])
                    ->pluck('type')
                    ->filter() // Filter out empty values
                    ->values();

                if ($types->count() !== $types->unique()->count()) {
                    // If there is an error, laravel will automatically redirect back to the form and flash the old input and errors to the session. 
                    //The error message will be displayed next to the relevant form fields in the view.
                    $validator->errors()->add("work_logs.$projectIndex.entries", 'Jeder Arbeitstyp darf pro Projekt nur einmal ausgewählt werden.');
                }
            }
        });

        return $validator->validate();
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
}

?>