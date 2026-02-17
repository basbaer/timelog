<?php

namespace App\Http\Controllers;

use App\Models\Role;

class RoleController extends Controller
{
    /**
     *  Get all Roles except 'Admin'.
     * 
     * @return Illuminate\Database\Eloquent\Collection
     */
   public function getWorkerRoles()
   {
       return Role::where('name', '!=', 'Admin')->get();
   }

    /**
     * Returns role of given id
     * 
     * @param int $id
     * 
     * @return Illuminate\Database\Eloquent\Collection
     */
    public function getRoleById($id)
    {
        return Role::where('id', $id)->first();
    }
}
