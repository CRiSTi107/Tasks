<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\User;

class Role extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
    ];


    public const USER = 'User';
    public const ADMIN = 'Admin';

    /**
     * Gets role ID by type.
     *
     * @param $name
     * @return ID
     */
    public static function GetRoleIDByName($name)
    {
        $role = Role::where([
            'name' => $name
        ])->get()->first();
        return $role->id;
    }

    /*public static function GetRoleName($jwtToken)
    {
        $data = $jwtToken->payload();
        $id = $data['id'];

        $role_id = User::where([
            'id' => $id
        ])->get()->first()->role_id;

        Role::where([
            'id' => $role_id
        ])->get()->first();
    }*/

    /**
     * Checks if a User is Admin by given Token
     * @param $jwtToken
     * @return bool
     */
    public static function IsAdmin($jwtToken)
    {
        $data = $jwtToken->payload();
        $id = $data['id'];

        $role_id = User::where([
            'id' => $id
        ])->get()->first()->role_id;

        return (self::GetRoleIDByName(self::ADMIN) == $role_id) ? true : false;
    }


}
