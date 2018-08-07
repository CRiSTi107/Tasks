<?php

namespace App;

use App\Http\Controllers\v1\UserController;
use Firebase\JWT\JWT;
use GenTux\Jwt\JwtPayloadInterface;
use GenTux\Jwt\JwtToken;
use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Support\Facades\Hash;
//use Illuminate\Support\Facades\DB;
use App\Role;

class User extends Model implements AuthenticatableContract, AuthorizableContract, JwtPayloadInterface
{
    use Authenticatable, Authorizable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'password', 'email', 'role_id', 'status'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public function getPayload()
    {
        return [
            'id' => $this->id,
            'exp' => time() + 7200,
            'context' => [
                'email' => $this->email
            ]
        ];
    }


    // Options for settings
    public const NAME     = 'new_name',
                 EMAIL    = 'new_email',
                 PASSWORD = 'new_password';


    /**
     * Login User
     *
     * @param $userEmail
     * @param $userPassword
     *
     * @return bool
     */
    public function login($userEmail, $userPassword)
    {
        $user = $this->where([
            'email' => $userEmail,
        ])->get()->first();

        if (!$user) {
            return false;
        }

        $password = $user->password;

        if (app('hash')->check($userPassword, $password)) {
            return $user;
        }

        return false;
    }


    /**
     * Register User
     * @param $userName
     * @param $userEmail
     * @param $userPassword
     * @return array
     */
    public function register($userName, $userEmail, $userPassword)
    {
        $user = $this->where([
            'email' => $userEmail
          ])->get()->first();

        $errors = array();

        //Check if a user with the same email already exists
        if($user) {
            $errors['errorMessage'] = 'An user with the same email already exists.';
            return $errors;
        }

        $user = $this->create(['name'     => $userName,
                               'email'    => $userEmail,
                               'password' => Hash::make($userPassword),
                               'role_id'  => Role::GetRoleIDByName(Role::USER),
                               'status'   => 0, // 0 - waiting for an admin to accept.
                                                // 1 - in order to be 1 it must be approved by an admin.
                              ]);

        return $user;
    }


    /**
     * Approve User
     * @param $userID - User ID to be approved.
     * @param JwtToken $jwtToken Admin token.
     * @return Instance of User or Array of error
     */
    public function approve($userID, JwtToken $jwtToken)
    {
        //Verify that if the sent token belongs to an admin
        if($jwtToken->validate()) // It's already validated once it goes here, but let's make sure
        {
            /*
            $data = $jwtToken->payload();
            $id = $data['id'];

            $role_id = $this->where([
                'id' => $id
            ])->get()->first()->role_id;
            */

            $errors = array();

            /* Function moved to Role.php : IsAdmin;
            if(! $role_id == Role::GetRoleIDByName(Role::ADMIN))
            {
                $errors['errorMessage'] = 'You are not an admin.';
                return $errors;
            }
            else
            {
                //Approve user account
                $user = $this->where([
                    'id' => $userID,
                ])->get()->first();
                $user->status = 1;
                $user->save();

                return $user;
            }
            */

            if(!Role::IsAdmin($jwtToken))
            {
                $errors['errorMessage'] = 'You are not an admin.';
                return $errors;
            }
            else
            {
                //Approve user account
                $user = $this->where([
                    'id' => $userID,
                ])->get()->first();
                $user->status = 1;
                $user->save();

                return $user;
            }
        }
    }


    /**
     * Check if a User is Approved
     * @param $userID
     * @return Returns true if user is approved, otherwise false.
     */
    public function isApproved($userID)
    {
        return ($this->where(['id' => $userID])->get()->first()['status']) ? true : false;
    }

    /*
    /**
     * Reset Password
     * @param $currentPassword
     * @param $newPassword
     * @param $jwtToken
     * /
    public function resetPassword($currentPassword, $newPassword, JwtToken $jwtToken)
    {
        if($jwtToken->validate())// It's already validated once it goes here, but let's make sure
        {
            //Get User Id from token
            $data = $jwtToken->payload();
            $id = $data['id'];

            //Get current hash of password
            $user = $this->where([
                'id' => $id
            ])->get()->first();

            $userPassword = $user->password;

            $errors = array();

            //Check if current password is correct to prove that he is the owner of account.
            if (! Hash::check($currentPassword, $userPassword))
            {
                $errors['errorMessage'] = 'Current password does not match.';
                return $errors;
            }
            else
            {
                //Update his new password.
                $user->password = Hash::make($newPassword);
                $user->save();

                return $user;
            }
        }
    } // Moved to changePassword.
    */

    /**
     * Change provided data (e.g. Name or Email Adress).
     * @param $payload
     * @param JwtToken $jwtToken
     */
    public function settings($payload, $userID)
    {
        //$data = array();
        foreach($payload as $setting => $value)
        {
            switch ($setting)
            {
                case self::NAME:
                    //$data[$setting] =
                        $this->changeName($userID, $value);
                    break;

                case self::EMAIL:
                    //$data[$setting] =
                        $this->changeEmail($userID, $value);
                    break;

                case self::PASSWORD:
                    //$data[$setting] =
                        $this->changePassword($userID, $value);

                default:
                    //This great feature is not implemented yet.
                    break;
            }
        }


        return $this; //$data
    }

    /** START - Settings and Admin function */

    // TODO: Add here new function for settings and admin routes.

    /**
     * Change Name
     * @param $userID
     * @param $newName
     * @return mixed
     */
    private function changeName($userID, $newName)
    {
        //$id = self::GetUserIDFromToken($jwtToken);

        $user = $this->where([
            'id' => $userID
        ])->get()->first();

        $user->name = $newName;
        $user->save();

        return $user;
    }

    /**
     * Change Email
     * @param $userID
     * @param $newEmail
     * @return mixed
     */
    private function changeEmail($userID, $newEmail)
    {
        //When User wants to change his Email Adress we should send a confirmation code, but that's a 'little bit' of work.
        //$id = self::GetUserIDFromToken($jwtToken);

        //Check if an user already has this email address.
        $userEmail = $this->where([
            'email' => $newEmail
        ])->get();

        if(count($userEmail) >= 1 && $userEmail->first()->id != $userID)
            throw new \Exception('This email address already exists.');

        $user = $this->where([
            'id' => $userID
        ])->get()->first();

        $user->email = $newEmail;
        $user->save();

        return $user;
    }

    /**
     * Change Password
     * @param $userID
     * @param $newPassword
     * @return mixed
     */
    private function changePassword($userID, $newPassword)
    {
        $user = $this->where([
            'id' => $userID
        ])->get()->first();

        $user->password = Hash::make($newPassword);
        $user->save();

        return $user;
    }

    /** END - Settings and Admin function */


    /**
     * Gets User Id from JwtToken
     * @param JwtToken $jwtToken
     * @return mixed
     */
    public function GetUserIDFromToken(JwtToken $jwtToken)
    {
        $data = $jwtToken->payload();
        return $data['id'];
    }


    /**
     * Promote a User to Admin.
     * @param $userID
     * @return mixed
     */
    public function promote($userID)
    {
        $user = $this->where([
            'id' => $userID
        ])->get()->first();
        
        $user->role_id = Role::GetRoleIDByName(Role::ADMIN);
        $user->save();

        return $user;
    }

    /**
     * Demote an Admin to User.
     * @param $userID
     * @return mixed
     */
    public function demote($userID)
    {
        $user = $this->where([
            'id' => $userID
        ])->get()->first();

        $user->role_id = Role::GetRoleIDByName(Role::USER);
        $user->save();

        return $user;
    }

}
