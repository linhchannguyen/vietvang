<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class User_Type extends Model
{
    protected $table = 'user_types';
    protected $primaryKey = 'type_id';

    public static function getAllUserType(){
        return User_Type::all();
    }
}
