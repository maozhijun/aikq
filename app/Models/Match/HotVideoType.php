<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/15
 * Time: 11:39
 */

namespace App\Models\Match;


use Illuminate\Database\Eloquent\Model;

class HotVideoType extends Model
{
//    protected $connection = "cms";
    const kStatusShow = 1, kStatusHide = 0;

    public static function allTypes() {
        return self::query()->where('status', self::kStatusShow)->orderBy('od', 'desc')->get();
    }

}