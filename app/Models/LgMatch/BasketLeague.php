<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/7
 * Time: 19:28
 */

namespace App\Models\LgMatch;


use Illuminate\Database\Eloquent\Model;

class BasketLeague extends Model
{
    public $connection = "match";

    protected $hidden = ['create_at', "spider_at"];
}