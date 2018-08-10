<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/15
 * Time: 11:38
 */

namespace App\Models\Match;


use Illuminate\Database\Eloquent\Model;

class HotVideo extends Model
{
//    protected $connection = "cms";
    const kShow = 1, kHide = 0;

    public static function getVideoByType($type = null, $size = 25) {
        $query = self::query();
        if (is_numeric($type)) {
            $query->where('type_id', $type);
        }
        $query->where('show', self::kShow);
        return $query->orderBy('od', 'desc')->orderBy('updated_at', 'desc')->take($size)->get();

    }

    public static function getVideoArray($type = null, $size = 25) {
        $video_array = [];
        $videos = self::getVideoByType($type, $size);
        foreach ($videos as $video) {
            $video_array[] = ['id'=>$video->id, 'title'=>$video->title, 'image'=>$video->image, 'player'=>$video->player, 'link'=>$video->link];
        }
        return $video_array;
    }

}