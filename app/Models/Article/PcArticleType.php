<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/8/3
 * Time: 16:43
 */

namespace App\Models\Article;


use Illuminate\Database\Eloquent\Model;

class PcArticleType extends Model
{
    const kStatusShow = 1, kStatusHide = 2;


    public static function allTypes() {
        return self::query()->where('status', self::kStatusShow)->orderByRaw('ifNull(od, 999)')->get();
    }

    public static function getTypeByTypeEn($type_en) {
        return self::query()->where('name_en', $type_en)->first();
    }

    public function appModel() {
        return [
            'id'=>$this->id,
            'name'=>$this->name,
            'name_en'=>$this->name_en,
        ];
    }
}