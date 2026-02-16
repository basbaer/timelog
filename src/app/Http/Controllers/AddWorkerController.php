<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Models\Role;

class AddWorkerController extends Controller
{
    public function show(): View
    {
        $roles = (new RoleController)->workerRoles();
        return view('admin/workers-add', ['roles' => $roles]);
    }

    public function createUser(Request $request)
    {
        $result = (new UserController)->create($request);
        $role = (new RoleController)->getRoleById($result['user']['role_id']);
        $result['role'] = $role;

        // show success page with generated password
        return redirect('/workers/add/success')->with('result', $result); 
    }
}
