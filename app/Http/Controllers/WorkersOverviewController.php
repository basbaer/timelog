<?php

namespace App\Http\Controllers;

use App\Models\User;

class WorkersOverviewController extends Controller
{
    public function show()
    {
        $workers = User::worker()->get();
        return view('admin/workers-overview', ['workers' => $workers]);
    }
}
