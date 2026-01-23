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
        $existingUsernames = User::where('username', $username)->first();
        # create unique username if already exists
        $i = 0;
        while ($existingUsernames) {
            $username = $username . $i;
            $existingUsernames = User::where('username', $username)->first();
            $i++;
        }


    

        echo $existingUsernames;

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
            
        $password = $this->generatePassword();

        # Hash password before storing
        $password = password_hash($password, PASSWORD_BCRYPT);

        // User::create([
        //     'username' => $username,
        //     'password' => $password,
        //     'first_name' => $request['first_name'],
        //     'last_name' => $request['last_name'],
        //     'role_id' => 1,
        // ]);
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
