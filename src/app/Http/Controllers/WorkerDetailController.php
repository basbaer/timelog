<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ForstwirtLogEntry;
use App\Models\ForstwirtWorkingType;
use App\Models\Project;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;

class WorkerDetailController extends Controller
{
    public function show($id)
    {
        try{
            // Get worker details from the database using the $id
            $worker = User::findOrFail($id);

            $log_entries = ForstwirtLogEntry::join('forstwirt_logs', 'forstwirt_log_entries.forstwirt_log_id', '=', 'forstwirt_logs.id')
                ->where('forstwirt_logs.user_id', $id)
                ->join('forstwirt_working_types', 'forstwirt_log_entries.working_type_id', '=', 'forstwirt_working_types.id')
                ->join('projects', 'forstwirt_logs.project_id', '=', 'projects.id')
                ->select(
                    'forstwirt_log_entries.hours as hours', 
                    'forstwirt_log_entries.comment as comment',
                    'forstwirt_logs.id as log_id',
                    'forstwirt_logs.date as date',
                    'forstwirt_logs.start as start',
                    'forstwirt_logs.end as end',
                    'forstwirt_logs.pause as pause',
                    'forstwirt_logs.sum as total',
                    'forstwirt_working_types.name as working_type_name', 
                    'projects.client as project_client',
                    'projects.location as project_location')
                ->get();

            foreach ($log_entries as $entry) {
                $entry->weekday = Carbon::parse($entry->date)->format('l');
                $entry->weekday = __('admin.' . $entry->weekday);
                $entry->hours = Carbon::parse($entry->hours)->format('H:i');
                $entry->start = Carbon::parse($entry->start)->format('H:i');
                $entry->end = Carbon::parse($entry->end)->format('H:i');
                $entry->pause = Carbon::parse($entry->pause)->format('H:i');
                $entry->total = Carbon::parse($entry->total)->format('H:i');
                $entry->date = Carbon::parse($entry->date)->format('d.m.y');
            }
            
            return view('admin/workers-detail', ['name' => $worker->first_name . ' ' . $worker->last_name, 'id' => $worker->id, 'log_entries' => $log_entries]);
            
        }catch (ModelNotFoundException $e) {
            // Handle the case where the worker is not found
            return redirect()->route('admin.workers.overview')->with('error', 'Worker not found.');
        }
        
    }
}
