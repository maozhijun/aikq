<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/8/16
 * Time: 18:10
 */

namespace App\Models\Admin;


use Illuminate\Database\Eloquent\Model;

/**
 * 直播管理员打卡表
 * Class LiveAccountSign
 * @package App\Models\Admin
 */
class LiveAccountSign extends Model
{
    const kStatusOn = 1, kStatusOff = 2;//1：上班，2：下班

    public function getStatusCn() {
        if ($this->status == self::kStatusOn) {
            return "值班中";
        } else {
            return "下班";
        }
    }

}