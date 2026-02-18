<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function showTimelog()
    {
        return view('timelog');
    }

    /**
     * Generate unique username based on the first three letters of his name
     * 
     * Numbers are added to the end of the username if it already exists, until a unique username is found
     * 
     * @param string $first_name
     * @return string
     */
    public function generateUsername(string $first_name)
    {;
        $username = strtolower(substr($first_name, 0, 3));

        // check if usersname already exists
        $existingUsernames = User::where('username', $username)->first();
        // create unique username if already exists
        $i = 0;
        while ($existingUsernames) {
            $username = $username . $i;
            $existingUsernames = User::where('username', $username)->first();
            // remove last character again
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

    /**
     * Create user and return generated password
     *  
     * The $result array is of the form:
     * [
     *   'user' => User::class,
     *   'password' => string
     * ]
     *
     * @param Request $request
     *
     * @return array
     */
    public function create(Request $request)
    {
        // Generate username and password
        $username = $this->generateUsername($request['first_name']);
            
        $password = $this->generatePassword();

        // Hash password before storing
        $password_hashed = Hash::make($password);

        $user = User::create([
            'username' => $username,
            'password' => $password_hashed,
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'role_id' => $request['role_id']
        ]);


        //check if it is first admin
        if (User::admin()->count() === 1) {
            //log in the created admin user
            Auth::login($user);
        }

        $result = [
            'user' => $user,
            'password' => $password
        ];

        return $result;
    }

}
