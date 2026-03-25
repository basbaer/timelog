<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WorkerCardController extends Controller
{
    public function show($id)
    {
        // Get worker details from the database using the $id
        try {
            $worker = User::findOrFail($id);

            // For now, we'll just return a view with the worker ID
        return view('admin/workers-card', [
            'id' => $id,
            'name' => $worker->first_name . ' ' . $worker->last_name,
            'role' => $worker->role->name, 
            'username' => $worker->username,
            'phone' => $worker->phone,
            'email' => $worker->email,
            'activation_code' => $worker->activation_code,
        ]);

        } catch (ModelNotFoundException $e) {
            // Handle the case where the worker is not found
            return redirect()->route('admin.workers.overview')->with('error', 'Worker not found.');
        }
        
        
    }
}
