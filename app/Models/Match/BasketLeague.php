<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/9/7
 * Time: 19:28
 */

namespace App\Models\Match;


use Illuminate\Database\Eloquent\Model;

class BasketLeague extends Model
{
//    public $connection = "match";

    protected $hidden = ['create_at', "spider_at"];

    /**
     * 获取数据库名
     */
    public static function getDatabaseName(){
        return env('DB_DATABASE_MATCH', 'moro');
    }
}