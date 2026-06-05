<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ForstwirtLogService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Carbon;

class WorkerDetailController extends Controller
{
    public function __construct(
        private readonly ForstwirtLogService $forstwirtLogService
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
            $last_date = null;

            $log_entries = collect();

            if ($worker->isForstwirt()) {
                $log_entries = $this->forstwirtLogService->getLogsFromTo(
                    $worker->id,
                    $first_of_current_month,
                    $last_of_current_month
                )->map(function ($entry) use (&$last_date) {
                    $entry->weekday = __('admin.' . Carbon::parse($entry->date)->format('l'));
                    $entry->hours = Carbon::parse($entry->hours)->format('H:i');
                    $entry->start = Carbon::parse($entry->start)->format('H:i');
                    $entry->end = Carbon::parse($entry->end)->format('H:i');
                    $entry->pause = Carbon::parse($entry->pause)->format('H:i');
                    $entry->total = Carbon::parse($entry->total)->format('H:i');

                    // Show date if it's the first entry of the day, otherwise hide it
                    if ($last_date !== $entry->date) {
                        $entry->show_date = true;
                        $last_date = $entry->date;
                    } else {
                        $entry->show_date = false;
                    }

                    $entry->date = Carbon::parse($entry->date)->format('d.m.y');

                    return $entry;
                });
            }

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
