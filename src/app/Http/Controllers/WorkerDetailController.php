<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\WorkerLogService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;

class WorkerDetailController extends Controller
{
    public function __construct(
        private readonly WorkerLogService $workerLogService
    ) {}

    public function show(int $id)
    {
        try{
            // Get worker details from the database using the $id
            $worker = User::findOrFail($id);

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

            $log_entries = $this->workerLogService->getLogsFor(
                $worker,
                $first_of_current_month,
                $last_of_current_month
            );

            $month = $currentMonth->translatedFormat('F Y');
            $previousMonth = $currentMonth->copy()->subMonth()->format('Y-m');
            $nextMonth = $currentMonth->copy()->addMonth()->format('Y-m');
            
            return view('admin/workers-detail', [
                'name' => $worker->first_name . ' ' . $worker->last_name,
                'id' => $worker->id,
                'log_entries' => $log_entries,
                'month' => $month,
                'previousMonth' => $previousMonth,
                'nextMonth' => $nextMonth,
            ]);
            
        }catch (ModelNotFoundException $e) {
            // Handle the case where the worker is not found
            return redirect()->route('admin.workers.overview')->with('error', 'Worker not found.');
        }
        
    }
}
