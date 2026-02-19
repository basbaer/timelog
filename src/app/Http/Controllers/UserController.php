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

    public function generateActivationCode()
    {
        //generate random code of 19 characters
        //structure: xxxx-xxxx-xxxx-xxxx
        $code = '';
        for ($i = 0; $i < 4; $i++) {
            $code .= bin2hex(random_bytes(2));
            if ($i < 3) {
                $code .= '-';
            }
        }
        return $code;
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
     * @return User
     */
    public function create(Request $request)
    {
        // Generate username and password
        $username = $this->generateUsername($request['first_name']);
            
        $activation_code = $this->generateActivationCode();

        $user = User::create([
            'username' => $username,
            'password' => null,
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'role_id' => $request['role_id'],
            'activation_code' => $activation_code
        ]);

        return $user;
    }

}
