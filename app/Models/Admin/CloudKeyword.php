<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Model;

class CloudKeyword extends Model
{
    public static function getKeyWord(){
        $query = CloudKeyword::query();
        $query->where('status','=',1);
        return $query->take(30)->get();
    }
}
