<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

/**
 * Created by PhpStorm.
 * User: ricky
 * Date: 2017/3/8
 * Time: 16:11
 */
class AdConf extends Model
{

    const CMS_SHD_CHANNEL_CODE_KEY = 'CMS_SHD_CHANNEL_CODE_KEY';//需验证线路的高清验证码

    protected $primaryKey = 'key';
    protected $keyType = 'string';
    public $timestamps = false;//不自动维护 created_at , updated_up

    public static function getValue($key, $default = null) {
        $adConf = self::query()->find($key);
        if (isset($adConf)) {
            return $adConf->value;
        }
        return $default;
    }


}