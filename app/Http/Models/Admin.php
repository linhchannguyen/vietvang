<?php

namespace App\Http\Models;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
// namespace App\Http\Models;
// use Illuminate\Database\Eloquent\Model;

class Admin extends Authenticatable
{
    use Notifiable;

    protected $table = 'admins';
    protected $primaryKey = 'id';

    public static function loginSuccess($email){
        Admin::where('email', $email)
            ->update([
                'attempt' => 0,
                'last_access' => date('Y-m-d H:i:s'),
                ]);
    }
    public static function updateAccount($email){
        Admin::where('email', $email)
            ->update([
                'token' => null,
                'activated' => 1,
                'attempt' => 0,
                'last_access' => date('Y-m-d H:i:s'),
            ]);
    }

    public static function updateAttemptLoginFail($email, $data){
        Admin::where('email', $email)
            ->update([
            'attempt' => ($data) + 1,
            'last_access' => date('Y-m-d H:i:s'),
            ]);
    }

    public static function blockAccount($email){
        Admin::where('email', $email)
            ->update([
            'token' => null,
            'activated' => 0,
            'last_access' => date('Y-m-d H:i:s'),
            ]);
    }

    /**
     * Logout
     * Admin logout and delete token
     */
    public static function adminLogout($data){
        Admin::where('token', '=', $data)
            ->update([
                'token' => null,
                'token_expire' => null,
            ]);
    }

    public static function getAllAdmInfo(){
        return Admin::all();
    }    

    public static function getAdInfo($id){
        return Admin::where('id', $id)->first();
    }    

    public static function findUserByToken($data){
        return Admin::where('token', $data)->first();
    }

    public static function findAdminByEmail($data){
        return Admin::where('email', $data)->first();
    }
}
