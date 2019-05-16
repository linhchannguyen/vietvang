<?php

namespace App;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

/**
 * Create by: Nguyen Linh Chan
 * Date: 13/5/2019
 * Place: Viet Vang Company
 */

class User extends Authenticatable
{
    use Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * Get all user
     * Eloquent get all user into users table
     */
    public static function getAllUser()
    {
        return User::select('*')
                ->leftJoin('admins', 'users.ad_id', '=', 'admins.admin_id')
                ->leftJoin('genders', 'users.gender_id', '=', 'genders.gender_id')
                ->leftJoin('user_types', 'users.role', '=', 'user_types.type_id')
                ->get();
    }

    /**
     * Logout
     * User logout and delete token
     */
    public static function userLogout($data){
        User::where('token', '=', $data)
            ->update([
                'token' => null,
                'token_expire' => null,
            ]);
    }
    public static function loginSuccess($email){
        User::where('email', $email)
            ->update([
                'attempt' => 0,
                'last_access' => date('Y-m-d H:i:s'),
                ]);
    }

    public static function updateAccount($email){
        User::where('email', $email)
            ->update([
                'token' => null,
                'activated' => 1,
                'attempt' => 0,
                'last_access' => date('Y-m-d H:i:s'),
            ]);
    }

    public static function updateAttemptLoginFail($email, $data){
        User::where('email', $email)
            ->update([
            'attempt' => ($data) + 1,
            'last_access' => date('Y-m-d H:i:s'),
            ]);
    }

    public static function blockAccount($email){
        User::where('email', $email)
            ->update([
            'token' => null,
            'activated' => 0,
            'last_access' => date('Y-m-d H:i:s'),
            ]);
    }

    public static function findUserByEmail($data){
        return User::where('email', $data)->first();
    }

    public static function findUserByToken($data){
        return User::where('token', $data)->first();
    }

    public static function findUserByID($id){
        return User::find($id);
    }
}
