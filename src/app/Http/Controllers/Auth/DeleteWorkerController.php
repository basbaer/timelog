<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Notifications\Notification;

class DeleteWorkerController extends Controller
{
    public function deleteWorker($id)
    {
        // Handle worker deletion logic here
        if(User::find($id)) {
            User::destroy($id);
            // Redirect back to the workers overview page after deletion
            return redirect()->route('admin.workers.overview')->with('success', 'Worker deleted successfully.');
        } else {
            return redirect()->route('admin.workers.overview')->with('error', 'Worker not found. Please try again.');
        }
        

        
    }
}
