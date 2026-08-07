<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

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

    public function changePassword(int $worker_id, Request $request)
    {

        $request->validate([
            'old_password' => 'required',
            'password' => 'required|min:6|confirmed',
        ]);

        $worker = User::find($worker_id);
        if (Hash::check($request->old_password, $worker->password)) {
            $worker->password = Hash::make($request->password);
            $worker->save();
            return redirect()->back()->with('success', 'Password changed successfully.');
        } else {
            return redirect()->back()->withErrors(['old_password' => __('error.old_password_incorrect')]);
        }

        return redirect()->back()->with('success', 'Password change functionality is not implemented yet.');
    }
}
