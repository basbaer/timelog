<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /**
     * Show the login page or redirect to the projects overview if there is already a user in the database
     */
    public function show()
    {
        // check if there is a user in the db
        if (false) {
            // return redirect('/projects');
            
        // if there is no existing user, a admin is created
        } elseif (User::count() === 0) {
            return view('admin/auth/admin-register')->with('adminId', Role::admin()->id);
        } else {
            return view('logIn');
        }
    }

    public function login(Request $request)
    {
        // Validate the input
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required',
        ]);
 
        // Attempt to log in
        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            // Regenerate session for security
            $request->session()->regenerate();
 
            /** @var User $user */
            $user = Auth::user();
 
            // Redirect dependig on the role of the user
            if ($user->isAdmin()) {
                return redirect()->intended('/projects')->with('success', 'Welcome back!');
            } else {
                return redirect()->intended('/timelog')->with('success', 'Welcome back!');
            }
        }
 
        // If login fails, redirect back with error
        return back()
            ->withErrors(['username' => 'The provided credentials do not match our records.'])
            ->onlyInput('username');
    }
}
