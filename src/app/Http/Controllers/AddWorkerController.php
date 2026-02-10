<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Controllers\RoleController;

class AddWorkerController extends Controller
{
    public function show(): View
    {
        $roles = (new RoleController)->workerRoles();
        return view('admin/workers-add', ['roles' => $roles]);
    }
}
