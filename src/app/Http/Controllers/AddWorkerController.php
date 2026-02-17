<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;

class AddWorkerController extends Controller
{
    public function show(): View
    {
        $roles = (new RoleController)->getWorkerRoles();
        return view('admin/workers-add', ['roles' => $roles]);
    }

    /**
    * Create user and redirect to success page with generated password
    * 
    * The $result array is of the form:
    * [
    *   'user' => User,
    *   'password' => string,
    *   'role' => string
    * ]
    *
    * @param Request $request
    * @return Illuminate\Http\RedirectResponse
    *
    */
    public function createUser(Request $request)
    {
        $result = (new UserController)->create($request);
        
        // get role name of created user
        $role = (new RoleController)->getRoleById($result['user']['role_id'])->value('name');
        $result['role'] = $role;

        // show success page with generated password
        return redirect('/workers/add/success')->with('result', $result); 
    }
}
