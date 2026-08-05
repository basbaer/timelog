<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;

class DeleteWorkerController extends Controller
{
    public function deleteWorker(int $worker_id)
    {
        // Handle worker deletion logic here
        if(User::find($worker_id)) {
            User::destroy($worker_id);
            // Redirect back to the workers overview page after deletion
            return redirect()->route('admin.workers.overview')->with('success', 'Worker deleted successfully.');
        } else {
            return redirect()->route('admin.workers.overview')->with('error', 'Worker not found. Please try again.');
        }
        

        
    }
}
