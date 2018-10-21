<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/10/17
 * Time: 16:21
 */

namespace App\Models\Label;


use Illuminate\Database\Eloquent\Model;

class LabelVideo extends Model
{
    public $timestamps = false;

    public static function saveLabelVideo($label, $video_id, $date) {
        if (!isset($label)) {
            return;
        }
        $label_id = $label->id;
        $query = self::query()->where('label_id', $label_id);
        $query->where('video_id', $video_id);
        $labelVideo = $query->first();
        if (!isset($labelVideo)) {
            $labelVideo = new LabelVideo();
            $labelVideo->label_id = $label_id;
            $labelVideo->video_id = $video_id;
            $labelVideo->created_at = $date;
            $labelVideo->save();
        }
    }

}