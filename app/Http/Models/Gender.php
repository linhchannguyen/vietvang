<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;

class Gender extends Model
{
    protected $table = 'genders';
    protected $primaryKey = 'gender_id';

    public static function getAllGender(){
        return Gender::all();
    }
}
