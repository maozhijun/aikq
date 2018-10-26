<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/10/16
 * Time: 15:39
 */

namespace App\Models\Match;

use App\Models\Label\Label;
use Illuminate\Database\Eloquent\Model;

class RelationVideo extends Model
{

    public static function relationVideos(array $labels, $count) {
        $lidStr = "";
        foreach ($labels as $label) {
            $labelEntity = Label::query()->where('label', $label)->first();
            if (!isset($labelEntity)) continue;
            $id = $labelEntity->id;
            if (empty($lidStr)) {
                $lidStr .= $id;
            } else {
                $lidStr .= ",".$id;
            }
        }

        $query = self::query();
        $query->join('label_videos', 'label_videos.video_id', '=', 'relation_videos.id');
        $query->whereExists(function ($exQuery) use ($lidStr) {
            $exQuery->selectRaw("1");
            $exQuery->from("label_groups");
            $exQuery->whereRaw("lid_main in (".$lidStr.")");
            $exQuery->whereRaw("(lid_same = label_videos.label_id or label_videos.label_id in (".$lidStr.") ) ");
        });
        $query->orderByDesc("relation_videos.created_at");
        $query->take($count);
        $query->select("relation_videos.*");

        return $query->get();
    }

}