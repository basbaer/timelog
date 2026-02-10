<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;

class RoleController extends Controller
{
   public function workerRoles()
   {
       return Role::where('name', '!=', 'Admin')->get();
   }
}
