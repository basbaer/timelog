<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LogoutController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
         Auth::logout();
 
        // Invalidate session
        $request->session()->invalidate();
        $request->session()->regenerateToken();
 
        return redirect('/')->with('success', __('auth.logout_success'));
    }
}
