<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class WorkerDetailController extends Controller
{
    public function show($id)
    {
        try{
            // Get worker details from the database using the $id
            $worker = User::findOrFail($id);
            
            return view('admin/workers-detail', ['name' => $worker->first_name . ' ' . $worker->last_name, 'id' => $worker->id]);
            
        }catch (ModelNotFoundException $e) {
            // Handle the case where the worker is not found
            return redirect()->route('admin.workers.overview')->with('error', 'Worker not found.');
        }
        
    }
}
