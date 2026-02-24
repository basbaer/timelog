<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use \Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

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
            'username' => 'required|string',
            'activation_code' => 'required|string',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $activationCode = $request->input('activation_code');

        // check the activation code against your database
        try {
            $user = User::where('username', $request->input('username'))
                ->where('activation_code', $activationCode)
                ->firstOrFail();
            // Mark the user as activated in the database
            $user->activation_code = null; // Clear the activation code

            // Set the user's password
            $user->password = Hash::make($request->input('password'));

            $user->save();

            //log the user in
            Auth::login($user);

            return redirect('/')->with('success', __('error.account_activated'));

        } catch (ModelNotFoundException $e) {
            // User with the given username and activation code not found
            return back()->withErrors(['invalid_activation_code' => __('error.invalid_activation_code')]);
        }
        
    }
}
