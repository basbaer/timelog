<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ActivationController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function show(): View
    {
        return view('activationForm');
    }

    public function activate(Request $request): RedirectResponse
    {
        $request->validate([
            'activation_code' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $activationCode = $request->input('activation_code');

        // Here you would typically check the activation code against your database
        // For demonstration, let's assume any non-empty code is valid
        if ($activationCode === 'VALID_CODE') {
            // Mark the user as activated in the database
            // For example: User::where('activation_code', $activationCode)->update(['activated' => true]);

            return redirect('/')->with('success', 'Your account has been activated successfully!');
        } else {
            return back()->withErrors(['activation_code' => 'Invalid activation code. Please try again.']);
        }
    }
}
