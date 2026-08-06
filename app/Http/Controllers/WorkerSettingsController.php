<?php

namespace App\Http\Controllers;


class WorkerSettingsController extends Controller
{
    public function show(int $worker_id)
    {
        // Logic to retrieve worker settings based on $worker_id
        // For example, you might fetch the worker's settings from the database
        // and pass them to a view.

        // Example:
        // $workerSettings = WorkerSettings::where('worker_id', $worker_id)->first();
        // return view('worker.settings', compact('workerSettings'));

        return view('worker-settings', ['worker_id' => $worker_id]);
    }
}
