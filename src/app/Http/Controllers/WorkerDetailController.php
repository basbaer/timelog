<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class WorkerDetailController extends Controller
{
    public function show($id)
    {
        // Get worker details from the database using the $id
        $worker = User::findOrFail($id);
        
        return view('admin/workers-detail', ['name' => $worker->first_name . ' ' . $worker->last_name]);
    }
}
