<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/15
 * Time: 11:38
 */

namespace App\Models\Match;


use App\Models\Tag\Tag;
use App\Models\Tag\TagRelation;
use Illuminate\Database\Eloquent\Model;

class HotVideo extends Model
{
//    protected $connection = "cms";
    const kShow = 1, kHide = 0;

    const kPlayerArrayCn = [MatchLiveChannel::kPlayerAuto=>'自动选择', MatchLiveChannel::kPlayerIFrame=>'iFrame', MatchLiveChannel::kPlayerM3u8=>'m3u8',
        MatchLiveChannel::kPlayerFlv=>'flv', MatchLiveChannel::kPlayerRTMP=>'rtmp', MatchLiveChannel::kPlayerExLink=>'外链', MatchLiveChannel::kPlayerMp4=>'Mp4'];

    const kPlatformArray = [MatchLiveChannel::kPlatformAll=>"全部", MatchLiveChannel::kPlatformPC=>"电脑", MatchLiveChannel::kPlatformWAP=>"手机"];

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

    public function playerCn() {
        if (isset(self::kPlayerArrayCn[$this->player])) {
            return self::kPlayerArrayCn[$this->player];
        } else {
            return "";
        }
    }

    public function platformCn() {
        if (isset(self::kPlatformArray[$this->player])) {
            return self::kPlatformArray[$this->player];
        } else {
            return "";
        }
    }

    public function tagsCn() {
        $id = $this->id;
        $tags = TagRelation::getTagRelations(TagRelation::kTypeVideo, $id);
        $tagCn = "";
        if (isset($tags["sport"])) {
            $tagCn .= $tags["sport"]["name"];
        }
        if (isset($tags["match"])) {
            $this->appendTagCn($tags["match"], $tagCn);
        }
        if (isset($tags["team"])) {
            $this->appendTagCn($tags["team"], $tagCn);
        }
        if (isset($tags["player"])) {
            $this->appendTagCn($tags["player"], $tagCn);
        }
        return $tagCn;
    }

    protected function appendTagCn($array, &$cn) {
        foreach ($array as $tag) {
            if (empty($cn)) {
                $cn .= $tag->name;
            } else {
                $cn .= "，" . $tag->name;
            }
        }
    }

}