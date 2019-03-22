<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2019/2/18
 * Time: 15:14
 */

namespace App\Models\Tag;


use App\Models\Article\PcArticle;
use App\Models\Match\HotVideo;
use App\Models\Subject\SubjectVideo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TagRelation extends Model
{
    const kTypeArticle = 1, kTypeVideo = 2, kTypePlayBack = 3;//1：文章，2：视频/集锦，3：录像/回放
    const kTypeArray = [self::kTypeArticle, self::kTypeVideo, self::kTypePlayBack];

    public static function hasTag($type, $source_id, $tag_id) {
        $query = self::query()->where("type", $type)->where("source_id", $source_id);
        $query->where("tag_id", $tag_id);
        return $query->count() > 0;
    }

    public static function firstTag($type, $source_id, $tag_id) {
        $query = self::query()->where("type", $type)->where("source_id", $source_id);
        $query->where("tag_id", $tag_id);
        return $query->first();
    }

    public static function saveRelation($type, $source_id, $tag_id) {
        $relation = new TagRelation();
        $relation->type = $type;
        $relation->source_id = $source_id;
        $relation->tag_id = $tag_id;
        $relation->save();
        return $relation;
    }

    /**
     * 一级标签只有一个
     * @param $type
     * @param $source_id
     * @param $sport
     */
    public static function saveFirstRelation($type, $source_id, $sport) {
        $query = self::query()->where("type", $type)->where("source_id", $source_id);
        $query->join("tags", "tags.id", "=", "tag_relations.tag_id");
        $query->where("tags.level", "=", Tag::kLevelOne);
        $query->select("tag_relations.*");
        $firstRelation = $query->first();
        if (isset($firstRelation)) {
            if ($firstRelation->tag_id != $sport) {
                $firstRelation->tag_id = $sport;
                $firstRelation->save();
            }
        } else {
            self::saveRelation($type, $source_id, $sport);
        }
    }

    /**
     * 保存标签关系
     * @param $sport
     * @param $type   关系类型，1：文章，2：视频，3：录像
     * @param $source_id  类型的ID
     * @param array $tags  标签数组 格式：["match"=>[ ["tag_id"=>xxx, "name"=>xx, "level"=>x],... ],
     *                                      "team"=>[ ["tag_id"=>xx, "name"=>xx, "id"=>xx, "level"=>xxx],.. ],
     *                                      "player"=>[ ["tag_id"=>xx, "name"=>xx, "level"=>x],.. ] ]
     */
    protected static function saveTagRelation($sport, $type, $source_id, array $tags) {
        if (!is_numeric($source_id) || $source_id < 1 || !in_array($type, self::kTypeArray)
            || !isset($tags) || count($tags) == 0 || !isset($sport) ) {
            return;
        }
        //竞技标签
        self::saveFirstRelation($type, $source_id, $sport);

        //赛事标签保存
        $matches = isset($tags["match"]) ? $tags["match"] : [];
        foreach ($matches as $match) {
            //判断是否有标签
            $tag_id = $match["tag_id"];
            $hasTag = self::hasTag($type, $source_id, $tag_id);
            if (!$hasTag) {
                self::saveRelation($type, $source_id, $tag_id);
            }
        }

        //球队标签保存
        $teams = isset($tags["team"]) ? $tags["team"] : [];
        foreach ($teams as $team) {
            $tag_id = $team["tag_id"];
            if (empty($tag_id)) {//无标签ID
                $team_name = $team["name"];
                $tag = Tag::saveTag($team_name, $sport, Tag::kLevelThree);//新标签保存，旧标签获取对象
                if (isset($tag)) {
                    $tag_id = $tag->id;
                }
            }
            if (!empty($tag_id)) {
                $hasTag = self::hasTag($type, $source_id, $tag_id);//判断是否有标签
                if (!$hasTag) {
                    self::saveRelation($type, $source_id, $tag_id);
                }
            }
        }

        //球员标签
        $players = isset($tags["player"]) ? $tags["player"] : [];
        foreach ($players as $player) {
            $tag_id = $player["tag_id"];
            if (empty($tag_id)) {
                $player_name = $player["name"];

                $tag = Tag::saveTag($player_name, $sport, Tag::kLevelFour);//新标签保存，旧标签获取对象
                if (isset($tag)) {
                    $tag_id = $tag->id;
                }
            }
            if (!empty($tag_id)) {
                $hasTag = self::hasTag($type, $source_id, $tag_id);//判断是否有标签
                if (!$hasTag) {
                    self::saveRelation($type, $source_id, $tag_id);
                }
            }
        }

    }

    /**
     * 保存文章关系标签
     * @param $sport
     * @param $source_id
     * @param array $tags
     */
    public static function saveArticleTagRelation($sport, $source_id, array $tags) {
        self::saveTagRelation($sport,self::kTypeArticle, $source_id, $tags);
    }

    /**
     * 保存文章关系标签
     * @param $sport
     * @param $source_id
     * @param array $tags
     */
    public static function savePlayBackTagRelation($sport, $source_id, array $tags) {
        self::saveTagRelation($sport,self::kTypePlayBack, $source_id, $tags);
    }

    /**
     * 保存视频关系标签
     * @param $sport
     * @param $source_id
     * @param array $tags
     */
    public static function saveVideoTagRelation($sport, $source_id, array $tags) {
        self::saveTagRelation($sport,self::kTypeVideo, $source_id, $tags);
    }

    /**
     * 标签cell填充数据
     * @param $type
     * @param $id
     * @return mixed
     */
    public static function tagCellArray($type, $id) {
        $tags = TagRelation::getTagRelations($type, $id);
        $result["sports"] = Tag::sports();
        $result["tags"] = $tags;
        $result["sport"] = isset($tags["sport"]) ? $tags["sport"] : null;
        return $result;
    }

    public static function getTagRelations($type, $source_id) {
        $query = self::query();
        $query->join("tags", "tags.id", "=", "tag_relations.tag_id");
        $query->where("type", $type);
        $query->where("source_id", $source_id);
        $query->orderBy("tags.level")->orderBy("tag_relations.id");
        $query->selectRaw("tag_relations.id");
        $query->addSelect(["tag_relations.id", "tag_relations.tag_id", "tags.name", "tags.level", "tags.tid", "tags.sport"]);
        $tags = $query->get();
        $array = [];
        foreach ($tags as $tag) {
            $level = $tag["level"];
            if ($level == Tag::kLevelOne) {
                $array["sport"] = $tag;
            } else if ($level == Tag::kLevelTwo) {
                $array["match"][] = $tag;
            } else if ($level == Tag::kLevelThree) {
                $array["team"][] = $tag;
            } else if ($level == Tag::kLevelFour) {
                $array["player"][] = $tag;
            }
        }
        return $array;
    }

    public static function getLeagueTagRelations($type, $source_id) {
        $query = self::query();
        $query->join("tags", "tags.id", "=", "tag_relations.tag_id");
        $query->where("type", $type);
        $query->where("source_id", $source_id);
        $query->where("tags.level", Tag::kLevelTwo);
        $query->orderBy("tags.level");
        $query->selectRaw("tag_relations.id");
        $query->addSelect(["tag_relations.id", "tag_relations.tag_id", "tags.name", "tags.level", "tags.tid", "tags.sport"]);
        $tags = $query->get()->toArray();
        return $tags;
    }

    public static function getTagWithSids($type, $source_id) {
        $query = self::query();
        $query->join("tags", "tags.id", "=", "tag_relations.tag_id");
        $query->where("type", $type);
        $query->where("source_id", $source_id);
        $query->orderBy("tags.level");
        $query->selectRaw("tag_relations.id");
        $query->addSelect(["tag_relations.id", "tag_relations.tag_id", "tags.name", "tags.level", "tags.tid"]);
        $tags = $query->get();
        return $tags;
    }

    public static function getFirstMatchTag4SL($type, $id) {
        $query = self::query();
        $query->join("tags", "tags.id", "=", "tag_relations.tag_id");
        $query->join("subject_leagues", function ($join) {
            $join->on(function ($on) {
                $on->whereRaw("subject_leagues.sport = tags.sport");
                $on->whereRaw("subject_leagues.lid = tags.tid");
            });
        });
        $query->where("tag_relations.type", $type);
        $query->where("tag_relations.source_id", $id);
        $query->where("tags.level", Tag::kLevelTwo);
        $query->select(["tags.sport", "tags.tid", "tags.name", "subject_leagues.name_en"]);
        $matchTag = $query->first();
        return $matchTag;
    }


    public static function getRelationsByTag($type, $sport, $level, $tagName, $pageNo = 1, $pageSize = 12) {
        if ($type == self::kTypeArticle) {
            $query = PcArticle::query();
            $tName = "pc_articles";
            $query->where($tName.".status", "=", PcArticle::kStatusPublish);
        } else if ($type == self::kTypeVideo) {
            $query = HotVideo::query();
            $tName = "hot_videos";
            $query->where($tName.".show", "=", HotVideo::kShow);
        } else if ($type == self::kTypePlayBack) {
            $query = SubjectVideo::query();
            $tName = "subject_videos";
        } else {
            return null;
        }


        $joinSql = "(select `tag_relations`.`source_id` from `tag_relations` INNER JOIN tags ON `tags`.`id` = `tag_relations`.`tag_id` ";
        $joinSql .= "WHERE `tags`.`sport` = ".$sport." AND `tags`.`level` = ".$level." AND `tags`.`name` LIKE '%".$tagName."%')";
        $joinTable = DB::raw($joinSql . " as tag ");

        $query->join($joinTable, $tName.'.id', '=', 'tag.source_id');

//        $query->whereExists(function ($eQuery) use ($tName, $sport, $level, $tagName) {
//            $eQuery->selectRaw("1");
//            $eQuery->from("tag_relations");
//            $eQuery->join("tags", "tags.id", "=", "tag_relations.tag_id");
//            $eQuery->where("tags.sport", $sport);
//            if (is_numeric($level)) {
//                $eQuery->where("tags.level", $level);
//            }
//            if (!empty($tagName)) {
//                $eQuery->where("tags.name", "like", "%$tagName%");
//            }
//            $eQuery->whereRaw("tag_relations.source_id = " . $tName . ".id1");
//        });

        if ($type == self::kTypeArticle) {
            $query->orderby('publish_at','desc');
        }

        $pages = $query->paginate($pageSize, ["*"], null, $pageNo);
        return $pages->items();
    }

    public static function getRelationsPageByTag($type, $sport, $level, $tagName, $pageNo = 1, $pageSize = 12) {
        if ($type == self::kTypeArticle) {
            $query = PcArticle::query();
            $tName = "pc_articles";
            $query->where($tName.".status", "=", PcArticle::kStatusPublish);
        } else if ($type == self::kTypeVideo) {
            $query = HotVideo::query();
            $tName = "hot_videos";
            $query->where($tName.".show", "=", HotVideo::kShow);
        } else if ($type == self::kTypePlayBack) {
            $query = SubjectVideo::query();
            $tName = "subject_videos";
        } else {
            return null;
        }

        $joinSql = "(select `tag_relations`.`source_id` from `tag_relations` INNER JOIN tags ON `tags`.`id` = `tag_relations`.`tag_id` ";
        $joinSql .= "WHERE `tags`.`sport` = ".$sport;
        if (is_numeric($level)) {
            $joinSql .= " AND `tags`.`level` = ".$level;
        }
        if (!empty($tagName)) {
            $joinSql .= " AND `tags`.`name` LIKE '%".$tagName."%'";
        }
        $joinSql .= ")";
        $joinTable = DB::raw($joinSql . " as tag ");

        $query->join($joinTable, $tName.'.id', '=', 'tag.source_id');

//        $query->whereExists(function ($eQuery) use ($tName, $sport, $level, $tagName) {
//            $eQuery->selectRaw("1");
//            $eQuery->from("tag_relations");
//            $eQuery->join("tags", "tags.id", "=", "tag_relations.tag_id");
//            $eQuery->where("tags.sport", $sport);
//            if (is_numeric($level)) {
//                $eQuery->where("tags.level", $level);
//            }
//            if (!empty($tagName)) {
//                $eQuery->where("tags.name", "like", "%$tagName%");
//            }
//            $eQuery->whereRaw("tag_relations.source_id = " . $tName . ".id");
//        });

        $query->orderby('updated_at','desc');
        $pages = $query->paginate($pageSize, ["*"], null, $pageNo);
        return $pages;
    }

    public static function getRelationsPageByTagId($type, $sport, $level, $tag_tId, $pageNo = 1, $pageSize = 12) {
        if ($type == self::kTypeArticle) {
            $query = PcArticle::query();
            $tName = "pc_articles";
        } else if ($type == self::kTypeVideo) {
            $query = HotVideo::query();
            $tName = "hot_videos";
        } else if ($type == self::kTypePlayBack) {
            $query = SubjectVideo::query();
            $tName = "subject_videos";
        } else {
            return null;
        }

        $joinSql = "(select `tag_relations`.`source_id` from `tag_relations` INNER JOIN tags ON `tags`.`id` = `tag_relations`.`tag_id` ";
        $joinSql .= "WHERE `tags`.`sport` = ".$sport;
        if (is_numeric($level)) {
            $joinSql .= " AND `tags`.`level` = ".$level;
        }
        if (!empty($tag_tId)) {
            $joinSql .= " AND `tags`.`tid` = " . $tag_tId;
        }
        $joinSql .= ")";
        $joinTable = DB::raw($joinSql . " as tag ");

        $query->join($joinTable, $tName.'.id', '=', 'tag.source_id');

//        $query->whereExists(function ($eQuery) use ($tName, $sport, $level, $tag_tId) {
//            $eQuery->selectRaw("1");
//            $eQuery->from("tag_relations");
//            $eQuery->join("tags", "tags.id", "=", "tag_relations.tag_id");
//            $eQuery->where("tags.sport", $sport);
//            if (is_numeric($level)) {
//                $eQuery->where("tags.level", $level);
//            }
//            if (!empty($tag_tId)) {
//                $eQuery->where("tags.tid", "=", $tag_tId);
//            }
//            $eQuery->whereRaw("tag_relations.source_id = " . $tName . ".id");
//        });

        $query->orderby('updated_at','desc');
        $pages = $query->paginate($pageSize, ["*"], null, $pageNo);
        return $pages;
    }
}