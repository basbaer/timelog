<?php

namespace App\Http\Controllers;

class WorkersOverviewController extends Controller
{
    public function show()
    {
        return view('admin/workers-overview');
    }

    public function getWorkers()
    {
        
    }
}
