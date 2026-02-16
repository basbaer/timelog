<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
            # remove last character again
            if ($existingUsernames){
                $username = substr($username, 0, -1);
                $i++;
            }else{
                break;
            }
            
        }
        
        return $username;
    }

    public function generatePassword()
    {
        $password = bin2hex(random_bytes(4)); // generates an 8 character hexadecimal password
        return $password;
    }

    public function create(Request $request)
    {
        # Generate username and password
        $username = $this->generateUsername($request['first_name']);
            
        $password = $this->generatePassword();

        # Hash password before storing
        $password_hashed = Hash::make($password);

        $user = User::create([
            'username' => $username,
            'password' => $password_hashed,
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'role_id' => $request['role_id']
        ]);

        $result = [
            'user' => $user,
            'password' => $password
        ];

        return $result;
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        $credentials['password'] = password_hash($credentials['password'], PASSWORD_ARGON2ID);
        User::create($credentials);
        return view('logIn');
    }
}
