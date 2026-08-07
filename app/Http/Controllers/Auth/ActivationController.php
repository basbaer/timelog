<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use \Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Hash;

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

            return redirect('/')->with('success', __('error.account_activated'));
        } catch (ModelNotFoundException $e) {
            // User with the given username and activation code not found
            return back()->withErrors(['invalid_activation_code' => __('error.invalid_activation_code')]);
        }
    }

    public function showPasswordResetForm(): View
    {
        return view('passwordResetForm');
    }

    public function resetPassword(Request $request): RedirectResponse
    {
        $request->validate([
            'username' => 'required|string',
        ]);

        $username = $request->input('username');

        // Find the user by username
        try {
            $user = User::where('username', $username)->firstOrFail();

            // Generate a new activation code
            //generate random code of 19 characters
            //structure: xxxx-xxxx-xxxx-xxxx
            $code = '';
            for ($i = 0; $i < 4; $i++) {
                $code .= bin2hex(random_bytes(2));
                if ($i < 3) {
                    $code .= '-';
                }
            }

            // Update the user's activation code in the database
            $user->activation_code = $code;
            $user->password = null; // Clear the password so the user can set a new one
            $user->save();

            // Here you would typically send the new activation code to the user via email or another method.
            // For this example, we'll just redirect back with a success message.
            return redirect('/')->with('success', __('error.password_reset_success'));
        } catch (ModelNotFoundException $e) {
            // User with the given username not found
            return back()->withErrors(['user_not_found' => __('error.user_not_found')]);
        }
    }
}
