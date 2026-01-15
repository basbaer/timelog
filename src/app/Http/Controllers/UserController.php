<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function showTimelog()
    {
        return view('timelog');
    }

    public function generateUsername(Request $request)
    {
        $firstName = $request->input('first_name');

        $username = strtolower(substr($firstName, 0, 3) . substr($lastName, 0, 3));

        #check if usersname already exists

        return $username;
    }

    public function generatePassword(Request $request)
    {
        $password = bin2hex(random_bytes(4)); // generates an 8 character hexadecimal password
        return $password;
    }

    public function register(Request $request)
    {
        $username = $this->generateUsername($request);
        $request->merge(['username' => $username]);
        $credentials = $request->only('username', 'password');

        $credentials['password'] = password_hash($credentials['password'], PASSWORD_ARGON2ID);
        User::create($credentials);
        return view('logIn');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        $credentials['password'] = password_hash($credentials['password'], PASSWORD_ARGON2ID);
        User::create($credentials);
        return view('logIn');
    }
}
