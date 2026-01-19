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

    /**
     * Generate username based on the first three letters of his name
     */
    public function generateUsername(string $first_name)
    {;
        $username = strtolower(substr($first_name, 0, 3));

        #check if usersname already exists

        return $username;
    }

    public function generatePassword()
    {
        $password = bin2hex(random_bytes(4)); // generates an 8 character hexadecimal password
        return $password;
    }

    public function register(Request $request)
    {
        # Generate username and password
        $username = $this->generateUsername($request['first_name']);
        $request->merge(['username' => $username]);
            
        $password = $this->generatePassword();
        $request->merge(['password' => $password]);

        # Hash password before storing
        $request['password'] = password_hash($request['password'], PASSWORD_BCRYPT);
    
        $request->merge(['role_id' => 1]);

        #User::create($request->all());
        return $request->all();
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        $credentials['password'] = password_hash($credentials['password'], PASSWORD_ARGON2ID);
        User::create($credentials);
        return view('logIn');
    }
}
