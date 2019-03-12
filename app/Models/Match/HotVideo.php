<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/1/15
 * Time: 11:38
 */

namespace App\Models\Match;


use App\Http\Controllers\Controller;
use App\Models\Subject\SubjectLeague;
use App\Models\Tag\Tag;
use App\Models\Tag\TagRelation;
use Illuminate\Database\Eloquent\Model;

class HotVideo extends Model
{
//    protected $connection = "cms";
    const kShow = 1, kHide = 2;

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
        if (array_key_exists($this->player,self::kPlayerArrayCn)) {
            return self::kPlayerArrayCn[$this->player];
        } else {
            return "";
        }
    }

    public function platformCn() {
        if (array_key_exists($this->player,self::kPlatformArray)) {
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

    /**
     * 视频终端链接
     * @param $id  视频ID
     * @return string
     */
    public static function getVideoDetailUrl($id) {
        //获取赛事标签
        $tag = TagRelation::getFirstMatchTag4SL(TagRelation::kTypeVideo, $id);
        while (strlen($id) < 4) {
            $id = "0" . $id;
        }
        if (isset($tag)) {
            return "/".$tag["name_en"]."/video".$id.".html";
        }
        return "/video".$id.".html";
    }

    /**
     * 视频终端静态化 路径
     * @param $id     视频ID
     * @return string 静态化路径 （/public/www|m/后的路径）
     */
    public static function getVideoDetailPath($id) {
        //获取赛事标签
        while (strlen($id) < 4) {
            $id = "0" . $id;
        }
        $first = substr($id, 0, 2);
        $second = substr($id, 2, 2);
        $last = $first . "/" . $second . "/" . $id . ".html";

        $tag = TagRelation::getFirstMatchTag4SL(TagRelation::kTypeVideo, $id);
        if (isset($tag)) {
            return "/".$tag["name_en"]."/video/".$last;
        }
        return "/video/detail/".$last;
    }

    /**
     * 视频终端静态化 路径
     * @param $id     视频ID
     * @return string 静态化路径 （/public/www|m/后的路径）
     */
    public static function getVideoDetailJsonPath($id) {
        //获取赛事标签
        while (strlen($id) < 4) {
            $id = "0" . $id;
        }
        $first = substr($id, 0, 2);
        $second = substr($id, 2, 2);
        $last = $first . "/" . $second . "/" . $id . ".json";
        return "/static/json/pc/video/player/".$last;
    }

    /**
     * 专题视频静态化路径
     * @param $name_en
     * @param $page
     * @return string
     */
    public static function getVideoListLeaguePath($name_en, $page) {
        $page = $page <= 0 ? 1 : $page;
        if ($page > 1) {
            $suffix = $page . '.html';
        } else {
            $suffix = "index.html";
        }
        return "/" . $name_en . "/video/" . $suffix;
    }

    /**
     * 右侧栏 tab 路径
     * @param $tab
     * @param $page
     * @return string /video/index.html || /video/basketball/index.html
     */
    public static function getVideoListTabPath($tab, $page) {
        $page = $page <= 0 ? 1 : $page;
        if ($page > 1) {
            $suffix = $page . '.html';
        } else {
            $suffix = "index.html";
        }
        if ($tab == "new" || empty($tab)) {
            return "/video/" . $suffix;
        }
        return "/video/" . $tab . "/" . $suffix;
    }

    /**
     * 篮球、足球 球星视频
     * @param $sport
     * @param $tagId
     * @param $page
     * @return string /video/index.html || /video/basketball/index.html
     */
    public static function getVideoListTagPath($sport, $tagId, $page) {
        $page = $page <= 0 ? 1 : $page;
        if ($page > 1) {
            $suffix = $page . '.html';
        } else {
            $suffix = "index.html";
        }
        if ($sport == Tag::kSportFootball) {
            return "/video/footballstar/" . $tagId . "/" . $suffix;
        } else {
            return "/video/basketballstar/" . $tagId . "/" . $suffix;
        }
    }


    /**
     * 相关视频列表
     * @param null $name_en
     * @param int $size
     * @return array
     */
    public static function getVideosByName($name_en = null, $size = 12) {
        if (isset($name_en)) {
            $sl = SubjectLeague::query()->where('name_en',$name_en)->first();
        }
        if (!isset($sl)) {
            $query = self::query();
            $query->where('hot_videos.show', self::kShow);
            $query->orderByDesc('hot_videos.created_at');
        } else {
            $query = self::query();
            $sport = $sl->sport;
            $lid = $sl->lid;
            $query->whereExists(function ($existsQuery) use ($sport, $lid) {
                $existsQuery->selectRaw("1");
                $existsQuery->from("tag_relations");
                $existsQuery->join("tags", "tag_relations.tag_id", "=", "tags.id");
                $existsQuery->where("tag_relations.type", TagRelation::kTypeVideo);
                $existsQuery->where("tags.sport", $sport);
                $existsQuery->where("tags.tid", $lid);
                $existsQuery->whereRaw("hot_videos.id = tag_relations.source_id");
            });
        }
        $videos = $query->take($size)->get();
        $videoArray = [];
        foreach ($videos as $video) {
            $link_id = $video->id;
            if (isset($sl)) {
                while (strlen($link_id) < 4) {
                    $link_id = "0" . $link_id;
                }
                $link = "/" . $name_en . "/video" . $link_id . ".html";
            } else {
                $link = HotVideo::getVideoDetailUrl($link_id);
            }
            $videoArray[] = ["link"=>$link, "title"=>$video->title, "image"=>$video->image, "id"=>$video->id];

        }
        return $videoArray;
    }

    /**
     * 获取视频列表页面的tab
     */
    public static function getVideoTabs() {
        return ["new", "basketball", "football", "basketballstar", "footballstar", "other"];
    }

    public static function getVideoLeagues() {
        //暂不使用目录 直接查询标签表
        $query = Tag::query();
        $query->join("subject_leagues", function ($join) {
            $join->on(function ($on) {
                $on->whereRaw("subject_leagues.sport = tags.sport");
                $on->whereRaw("subject_leagues.lid = tags.tid");
            });
        });
        $query->where("tags.level", Tag::kLevelTwo);
        $query->whereExists(function ($exists) {
            $exists->selectRaw("1");
            $exists->from("tag_relations");
            $exists->where("tag_relations.type", TagRelation::kTypeVideo);
            $exists->whereRaw("tag_relations.tag_id = tags.id");
        });
        $query->selectRaw("tags.id as tag_id");
        $query->addSelect(["subject_leagues.name_en", "tags.name", "tags.sport"]);
        return $query->get()->toArray();
    }

    /**
     * 获取所有球星栏目
     * @return array
     */
    public static function getVideoStars() {
        //暂不使用目录 直接查询标签表
        $query = Tag::query();
        $query->where("tags.level", Tag::kLevelFour);
        $query->whereExists(function ($exists) {
            $exists->selectRaw("1");
            $exists->from("tag_relations");
            $exists->where("tag_relations.type", TagRelation::kTypeVideo);
            $exists->whereRaw("tag_relations.tag_id = tags.id");
        });
        $query->selectRaw("tags.id as tag_id");
        $query->addSelect(["tags.name", "tags.sport"]);
        return $query->get()->toArray();
    }

    /**
     * 静态化视频终端页
     */
    public static function staticHotVideoDetailHtml($id) {
        $url = env('CMS_URL') . "/static/video/detail/" . $id;
        Controller::execUrl($url, 3);
    }

    /**
     * 静态化视频列表
     * @param $tag
     * @param $page
     * @return string
     */
    public static function staticHotVideosHtml($tag, $page) {
        $url = env('CMS_URL') . "/static/video/list/" . $tag . "/" . $page;
        return Controller::execUrl($url, 3);
    }

    /**
     * 静态化视频 赛事、联赛 列表
     * @param $name_en
     * @param $page
     * @return string
     */
    public static function staticHotVideosLeagueHtml($name_en, $page) {
        $url = env('CMS_URL') . "/static/video/list-leg/" . $name_en . "/" . $page;
        return Controller::execUrl($url, 3);
    }

    /**
     * 静态化视频 球星 列表
     * @param $tagId
     * @param $sport
     * @param $page
     * @return string
     */
    public static function staticHotVideosTagHtml($tagId, $sport, $page) {
        $url = env('CMS_URL') . "/static/video/list-tag/" . $tagId . "-" . $sport . "/" . $page;
        return Controller::execUrl($url, 3);
    }

}