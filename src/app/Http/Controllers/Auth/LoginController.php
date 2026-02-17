<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;

class LoginController extends Controller
{
    public function __invoke()
    {
        // check if there is a user in the db
        if (false) {
            // return redirect('/projects');
        } elseif (User::count() === 0) {
            return view('admin-register');
        } else {
            return view('logIn');
        }

        
    }
}
