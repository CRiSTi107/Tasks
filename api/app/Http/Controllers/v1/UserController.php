<?php
/**
 * Created by PhpStorm.
 * User: iongh
 * Date: 8/1/2018
 * Time: 3:37 PM
 */

namespace App\Http\Controllers\v1;


use App\Http\Controllers\Controller;
use App\User;
use App\Role;
use GenTux\Jwt\JwtToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    //Settings options
    public const SETTINGS_OPTIONS = [User::NAME, User::EMAIL, User::PASSWORD];


    /**
     * Login User
     *
     * @param Request $request
     * @param User $userModel
     * @param JwtToken $jwtToken
     *
     * @return \Illuminate\Http\JsonResponse
     * @throws \GenTux\Jwt\Exceptions\NoTokenException
     */
    public function login(Request $request, User $userModel, JwtToken $jwtToken)
    {
        $rules = [
            'email'    => 'required|email',
            'password' => 'required'
        ];

        $messages = [
            'email.required' => 'Email empty',
            'email.email'    => 'Email invalid',
            'password.required'    => 'Password empty'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ( ! $validator->passes()) {
            return $this->returnBadRequest($validator->errors());
        }

        $user = $userModel->login($request->email, $request->password);

        if ( ! $user) {
            return $this->returnNotFound('Username or password incorrect.');
        }

        $user = $userModel->where([
            'email' => $request->email
        ])->get()->first();

        $userID = $user->id;

        if (!$userModel->isApproved($userID)) {
            return $this->returnNotFound('You accout has not been approved yet.');
        }

        $token = $jwtToken->createToken($user);

        $data = [
            'user' => $user,
            'jwt'  => $token->token()
        ];

        return $this->returnSuccess($data);
    }


    /**
     * Register User
     * @param Request $request
     * @param User $userModel
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request, User $userModel)
    {
        $rules = [
            'name'     => 'required',
            'email'    => 'required|email', //|unique:email
            'password' => 'required'
        ];

        $messages = [
            'name.required'  => 'Name empty',
            'email.required' => 'Email empty',
            'email.email'    => 'Email invalid',
            //'email.unique'   => 'An user with the same email already exists.',
            'password.required'    => 'Password empty'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ( ! $validator->passes()) {
            return $this->returnBadRequest($validator->errors());
          }

        $user = $userModel->register($request->name, $request->email, $request->password);

        if($user instanceof User)
        {
            return $this->returnSuccess();
        }
        else // returned error from Model
        {
            return $this->returnNotFound($user['errorMessage']);
        }
    }


    /**
     * Approve User
     * @param Request $request
     * @param User $userModel
     * @param JwtToken $jwtToken
     * @return \Illuminate\Http\JsonResponse
     */
    public function approve(Request $request, User $userModel, JwtToken $jwtToken)
    {
        $rules = [
            'user_id' => 'required'
        ];

        $messages = [
            'user_id.required' => 'User ID not provided.'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ( ! $validator->passes()) {
            return $this->returnBadRequest($validator->errors());
        }

        $jwtToken->setToken($request->token);
        $user = $userModel->approve($request->user_id, $jwtToken);

        if($user instanceof User)
        {
            return $this->returnSuccess('Account has been approved.');
        }
        else
        {
            return $this->returnNotFound($user['errorMessage']);
        }

    }


    /**
     * Reset Password
     * @param Request $request
     * @param User $userModel
     * @param JwtToken $jwtToken
     */
    public function resetPassword(Request $request, User $userModel, JwtToken $jwtToken)
    {
        $rules = [
            //'user_id' => 'required'
            'current_password' => 'required',
            'new_password' => 'required'
        ];

        $messages = [
            'current_password.required' => 'Current password not provided.',
            'new_password.required' => 'New password not provided.'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ( ! $validator->passes()) {
            return $this->returnBadRequest($validator->errors());
        }

        $jwtToken->setToken($request->token);
        $user = $userModel->resetPassword($request->current_password, $request->new_password, $jwtToken);

        if($user instanceof User)
        {
            return $this->returnSuccess();
        }
        else
        {
            return $this->returnNotFound($user['errorMessage']);
        }

    }


    /**
     * Change own information
     * @param Request $request
     * @param User $userModel
     * @param JwtToken $jwtToken
     */
    public function settings(Request $request, User $userModel, JwtToken $jwtToken)
    {
        $jwtToken->setToken($request->token);

        $newSettings = $this->GetSettingsFromRequest($request);
        try {
            $user = $userModel->settings($newSettings, $userModel->GetUserIDFromToken($jwtToken));

            if($user instanceof User)
            {
                return $this->returnSuccess('Settings has been saved.');
            }
        }
        catch(\Exception $e) {
            return $this->returnNotFound($e->getMessage());
        }

    }


    /**
     * Change other user's information
     * @param Request $request
     * @param User $userModel
     * @param JwtToken $jwtToken
     */
    public function admin(Request $request, User $userModel, JwtToken $jwtToken)
    {
        // Check if user_id has been sent.
        $rules = [
            'user_id' => 'required'
        ];

        $messages = [
            'user_id.required' => 'User ID not provided.'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ( ! $validator->passes()) {
            return $this->returnBadRequest($validator->errors());
        }

        // Check if owner of token is an admin.
        $jwtToken->setToken($request->token);

        if(!Role::IsAdmin($jwtToken))
        {
            return $this->returnBadRequest('You are not an admin.');
        }

        $newSettings = self::GetSettingsFromRequest($request);
        $userID = $request->user_id;
        try {
            $user = $userModel->settings($newSettings, $userID);

            if($user instanceof User)
            {
                return $this->returnSuccess('Settings has been saved.');
            }
        }
        catch (\Exception $e)
        {
            return $this->returnNotFound($e->getMessage());
        }

    }

    /**
     * Get supported Settings from Request as Array
     * @param Request $request
     * @return array
     */
    private function GetSettingsFromRequest(Request $request)
    {
        $newSettings = array();

        //If we add more features in this way it's easier
        foreach(self::SETTINGS_OPTIONS as $setting)
        {
            if(isset($request->all()[$setting]))
            {
                $newSettings["{$setting}"] = $request->all()[$setting];
            }
        }

        return $newSettings;
    }


    /**
     * Promote a User to Admin.
     * @param Request $request
     * @param User $userModel
     * @param JwtToken $jwtToken
     * @return \Illuminate\Http\JsonResponse
     */
    public function promote(Request $request, User $userModel, JwtToken $jwtToken)
    {
        // Check if user_id has been sent.
        $rules = [
            'user_id' => 'required'
        ];

        $messages = [
            'user_id.required' => 'User ID not provided.'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ( ! $validator->passes()) {
            return $this->returnBadRequest($validator->errors());
        }

        // Check if owner of token is an admin.
        $jwtToken->setToken($request->token);

        if(!Role::IsAdmin($jwtToken))
        {
            return $this->returnBadRequest('You are not an admin.');
        }

        $user = $userModel->promote($request->user_id);

        if($user instanceof User)
        {
            return $this->returnSuccess('User has been promoted to Admin.');
        }
        else
        {
            return $this->returnNotFound();
        }
    }

    /**
     * Demote an Admin to User.
     * @param Request $request
     * @param User $userModel
     * @param JwtToken $jwtToken
     * @return \Illuminate\Http\JsonResponse
     */
    public function demote(Request $request, User $userModel, JwtToken $jwtToken)
    {
        // Check if user_id has been sent.
        $rules = [
            'user_id' => 'required'
        ];

        $messages = [
            'user_id.required' => 'User ID not provided.'
        ];

        $validator = Validator::make($request->all(), $rules, $messages);

        if ( ! $validator->passes()) {
            return $this->returnBadRequest($validator->errors());
        }

        // Check if owner of token is an admin.
        $jwtToken->setToken($request->token);

        if(!Role::IsAdmin($jwtToken))
        {
            return $this->returnBadRequest('You are not an admin.');
        }

        $user = $userModel->demote($request->user_id);

        if($user instanceof User)
        {
            return $this->returnSuccess('Admin has been demoted to User.');
        }
        else
        {
            return $this->returnNotFound();
        }
    }
}
