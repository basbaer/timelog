<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Support\Facades\DB;


class RoleController extends Controller
{
    /**
     *  Get all Roles except 'Admin'.
     * 
     * @return array 
     */
   public function workerRoles()
   {
       return Role::where('name', '!=', 'Admin')->get();
   }

    /**
     * Returns role of given id
     */
    public function getRoleById($id)
    {
        $role = Role::where('id', $id)->get()->value('name');
        return $role;
    }
}
