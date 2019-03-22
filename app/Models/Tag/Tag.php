<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2019/2/18
 * Time: 15:06
 */

namespace App\Models\Tag;

use App\Models\LgMatch\BasketLeague;
use App\Models\LgMatch\BasketTeam;
use App\Models\LgMatch\League;
use App\Models\LgMatch\Team;
use App\Models\Match\HotVideo;
use function GuzzleHttp\default_user_agent;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    const kSportFootball = 1, kSportBasketball = 2;
    const kLevelOne = 1, kLevelTwo = 2, kLevelThree = 3, kLevelFour = 4;//一级：竞技类型，二级：赛事，三级：球队，四级：球员
    const kLevelArray = [self::kLevelFour, self::kLevelThree, self::kLevelTwo, self::kLevelOne];
    const kSportArray = [self::kSportFootball, self::kSportBasketball];

    public function sportCn() {
        $sport = $this->sport;
        switch ($sport) {
            case self::kSportFootball:
                $cn = "足球";
                break;
            case self::kSportBasketball:
                $cn = "篮球";
                break;
            default: $cn = "";
        }
        return $cn;
    }

    public function levelCn() {
        $level = $this->level;
        switch ($level) {
            case self::kLevelOne:
                $cn = "一级";
                break;
            case self::kLevelTwo:
                $cn = "二级";
                break;
            case self::kLevelThree:
                $cn = "三级";
                break;
            case self::kLevelFour:
                $cn = "四级";
                break;
            default: $cn = "";
        }
        return $cn;
    }

    /**
     * 根据条件获取 目标 对应的ID
     * @param $name
     * @param $sport
     * @param $level
     * @return null
     */
    public static function findTid($name, $sport, $level) {
        if (!in_array($level, [self::kLevelTwo, self::kLevelThree, self::kLevelFour]) || !in_array($sport, [self::kSportFootball, self::kSportBasketball]) ) {
            return null;
        }

        switch ($level) {
            case self::kLevelTwo:
                if ($sport == self::kSportFootball) {
                    $query = League::query();
                } else {
                    $query = BasketLeague::query();
                }
                $obj = $query->where("name", $name)->first();
                break;
            case self::kLevelThree:
                if ($sport == self::kSportFootball) {
                    $query = Team::query();
                    $obj = $query->where("name", $name)->first();
                } else {
                    $query = BasketTeam::query();
                    $obj = $query->where("name_china", $name)->first();
                }
                break;
        }

        if (!isset($obj)) {
            return null;
        }
        return $obj->id;
    }

    /**
     * 保存标签,返回标签对象
     * @param $name
     * @param $sport
     * @param $level
     * @return Tag|Model|null|static
     */
    public static function saveTag($name, $sport, $level) {
        $repeatQuery = Tag::query()->where("sport", $sport)->where("level", $level)->where("name", $name);
        $tag = $repeatQuery->first();
        if (isset($tag)) {
            return $tag;
        }
        $tag = new Tag();
        $tid = self::findTid($name, $sport, $level);
        $tag->name = $name;
        $tag->sport = $sport;
        $tag->level = $level;
        $tag->tid = $tid;
        $tag->save();
        return $tag;
    }


    public static function leagueTags($type, $sport, $size = 9) {
        $query = Tag::query();
        $query->join("subject_leagues", function ($join) {
            $join->on(function ($on) {
                $on->whereRaw("subject_leagues.sport = tags.sport");
                $on->whereRaw("subject_leagues.lid = tags.tid");
            });
        });
        $query->whereExists(function ($eQuery) use ($type) {
            $eQuery->selectRaw("1");
            $eQuery->from("tag_relations");
            $eQuery->where("tag_relations.type", $type);
            $eQuery->whereRaw("tag_relations.tag_id = tags.id");
        });

        $query->where("tags.sport", $sport);
        $query->where("tags.level", self::kLevelTwo);
        $query->orderBy("subject_leagues.od")->orderBy("subject_leagues.id");
        $query->select(["subject_leagues.name", "subject_leagues.name_en", "subject_leagues.lid"]);
        $query->take($size);
        return $query->get();
    }

    public static function starTags($type, $sport, $size = 9) {
        $query = Tag::query();
        $query->whereExists(function ($eQuery) use ($type) {
            $eQuery->selectRaw("1");
            $eQuery->from("tag_relations");
            $eQuery->join("hot_videos", "hot_videos.id", "=", "tag_relations.source_id");
            $eQuery->where("hot_videos.show", HotVideo::kShow);
            $eQuery->where("tag_relations.type", $type);
            $eQuery->whereRaw("tag_relations.tag_id = tags.id");
        });

        $query->where("tags.sport", $sport);
        $query->where("tags.level", self::kLevelFour);
        $query->select(["tags.name", "tags.id"]);
        $query->take($size);
        return $query->get();
    }

    public static function isFirstTag($id) {
        $tag = self::query()->find($id);
        return isset($tag) && $tag["level"] == self::kLevelOne;
    }

    public static function sports() {
        return Tag::query()->where("level", Tag::kLevelOne)->orderBy("id")->get();
    }

}