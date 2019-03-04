<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/4/12
 * Time: 15:29
 */

namespace App\Http\Controllers\PC\Live;


use App\Http\Controllers\PC\MatchTool;
use App\Models\Match\HotVideo;
use App\Models\Match\HotVideoType;
use App\Models\Subject\SubjectLeague;
use App\Models\Tag\Tag;
use App\Models\Tag\TagRelation;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{

    const page_size = 20;
    //=====================================页面内容 开始=====================================//

    /**
     * 视频 列表
     * @param Request $request
     * @param $type
     * @param $page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function videos(Request $request, $type = "new", $page = 1) {
        $types = ["new"=>"最新", "basketball"=>"篮球", "football"=>"足球", "basketballstar"=>"篮球球星", "footballstar"=>"足球球星", "other"=>"其他"];
        $videos = $this->getVideos($type, $page);
        return $this->videosHtml($type, $types, $videos);
    }


    /**
     * 专题视频列表
     * @param Request $request
     * @param $name_en
     * @param int $page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function videosByNameEn(Request $request, $name_en, $page = 1) {
        $sl = SubjectLeague::getSubjectLeagueByEn($name_en);
        if (isset($sl)) {
            $types = ["new"=>"最新", "basketball"=>"篮球", "football"=>"足球", "basketballstar"=>"篮球球星", "footballstar"=>"足球球星", "other"=>"其他"];
            $videos = $this->getVideosByLeague($sl->sport, $sl->lid, $page);
            return $this->videosHtml($name_en, $types, $videos);
        }
        return $this->videos($request, $page);
    }

    /**
     * 篮球球星 视频列表
     * @param Request $request
     * @param $id    标签ID
     * @param $page  = 1 页码
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function videosByFootballStar(Request $request, $id, $page = 1) {
        $types = ["new"=>"最新", "basketball"=>"篮球", "football"=>"足球", "basketballstar"=>"篮球球星", "footballstar"=>"足球球星", "other"=>"其他"];
        $videos = $this->getVideosByTag(Tag::kSportFootball, $id, $page);
        return $this->videosHtml($id, $types, $videos);
    }

    /**
     * 足球球星 视频列表
     * @param Request $request
     * @param $id    标签ID
     * @param $page  = 1 页码
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function videosByBasketballStar(Request $request, $id, $page = 1) {
        $types = ["new"=>"最新", "basketball"=>"篮球", "football"=>"足球", "basketballstar"=>"篮球球星", "footballstar"=>"足球球星", "other"=>"其他"];
        $videos = $this->getVideosByTag(Tag::kSportBasketball, $id, $page);
        return $this->videosHtml($id, $types, $videos);
    }

    /**
     * 录像列表
     * @param $type
     * @param $types
     * @param $videos
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    protected function videosHtml($type, $types, $videos) {
        if (!isset($videos['videos'])) {
            return "";
        }
        $result['types'] = $types;
        $result['page'] = $videos['page'];
        $result["pageUrl"] = $this->pageUrl($type);
        $result['videos'] = $videos['videos'];
        $result["tags"] = isset($videos["tags"]) ? $videos["tags"] : null;
        $result["stars"] = isset($videos["stars"]) ? $videos["stars"] : null;
        $result["sport"] = isset($videos["sport"]) ? $videos["sport"] : null;
        $result['type'] = $type;
        $result['check'] = 'videos';
        return view('pc.video.list', $result);
    }

    public function videoDetailByNameEn(Request $request, $name_en, $id) {
        return $this->videoDetail($request, $id);
    }

    /**
     * 录像终端
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function videoDetail(Request $request, $id) {
        $id = intval($id);
        $video = HotVideo::query()->find($id);
        if (!isset($video)) return "";
        return $this->videoDetailHtml($video);
    }

    /**
     * 录像终端 html
     * @param HotVideo $video
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    protected function videoDetailHtml(HotVideo $video) {
        $result["video"] = $video;
        $id = $video["id"];

        //根据视频ID获取 赛事
        $tagQuery = TagRelation::query()->where("tag_relations.type", TagRelation::kTypeVideo);
        $tagQuery->join("tags", "tags.id", "=", "tag_relations.tag_id");
        $tagQuery->where("tags.level", Tag::kLevelTwo);
        $tagQuery->where("tag_relations.source_id", $id);
        $matchTags = $tagQuery->get();

        $name_en = "all";
        if (count($matchTags) > 0) {
            $tag = $matchTags[0];
            $sport = $tag["sport"];
            $lid = $tag["tid"];
            $sl = SubjectLeague::getSubjectLeagueByLid($sport, $lid);
            if (isset($sl)) {
                $name_en = $sl["name_en"];
                $result["def"] = ["name"=>$tag["name"], "name_en"=>$name_en ];
            }
        }

        try {
            $json = Storage::get("/public/static/json/pc/comboData/".$name_en.".json");
            $comboData = json_decode($json, true);
            $result["comboData"] = $comboData;
        } catch (\Exception $exception) {
        }
        return view('pc.video.detail', $result);
    }

    //=====================================页面内容 结束=====================================//


    //=====================================数据接口 开始=====================================//

    /**
     * 获取录像列表
     * @param $type
     * @param $pageNo
     * @return array|mixed
     */
    public function getVideos($type, $pageNo) {
        $pageSize = self::page_size;
        $query = HotVideo::query();
        $array = [];

        switch ($type) {
            case "basketball":
            case "football":
                if ($type == "basketball") {
                    $tag_id = Tag::kSportBasketball;
                } else {
                    $tag_id = Tag::kSportFootball;
                }
                $tags = Tag::leagueTags(TagRelation::kTypeVideo, $tag_id);///获取篮球、足球 赛事视频 列表
                $array["tags"] = $tags;
                $query->whereExists(function ($existsQuery) use ($tag_id) {
                    $existsQuery->selectRaw("1");
                    $existsQuery->from("tag_relations");
                    $existsQuery->where("tag_relations.type", TagRelation::kTypeVideo);
                    $existsQuery->where("tag_relations.tag_id", $tag_id);
                    $existsQuery->whereRaw("tag_relations.source_id = hot_videos.id");
                });
                break;
            case "basketballstar":
            case "footballstar":
                if ($type == "basketballstar") {
                    $tag_id = Tag::kSportBasketball;
                } else {
                    $tag_id = Tag::kSportFootball;
                }
                $array["stars"] = Tag::starTags(TagRelation::kTypeVideo, $tag_id);
                $array["sport"] = $tag_id;
                $query->whereExists(function ($existsQuery) use ($tag_id) {
                    $existsQuery->selectRaw("1");
                    $existsQuery->from("tag_relations");
                    $existsQuery->join("tags", "tags.id", "=", "tag_relations.tag_id");
                    $existsQuery->where("tags.level", Tag::kLevelFour);
                    $existsQuery->where("tags.sport", $tag_id);
                    $existsQuery->where("tag_relations.type", TagRelation::kTypeVideo);
                    $existsQuery->whereRaw("tag_relations.source_id = hot_videos.id");
                });
                break;
            case "other":
                $query->whereNotExists(function ($notExistsQuery) {
                    $notExistsQuery->selectRaw("1");
                    $notExistsQuery->from("tag_relations");
                    $notExistsQuery->where("tag_relations.type", TagRelation::kTypeVideo);
                    $notExistsQuery->whereRaw("tag_relations.source_id = hot_videos.id");
                });
                break;
            default:
        }

        $query->where("hot_videos.show", HotVideo::kShow);
        $query->orderByDesc('updated_at');
        $page = $query->paginate($pageSize, ['*'], null, $pageNo);

        $videos = $page->items();
        $array['page'] = ['curPage'=>$page->currentPage(), 'total'=>$page->total(), 'pageSize'=>$pageSize, 'lastPage'=>$page->lastPage()];
        foreach ($videos as $video) {
            $array['videos'][] = self::hotVideo2Array($video);
        }
        return $array;
    }

    /**
     * 获取录像列表
     * @param $sport
     * @param $lid
     * @param $pageNo
     * @return array|mixed
     */
    public function getVideosByLeague($sport, $lid, $pageNo) {
        $pageSize = self::page_size;

        $tag = Tag::query()->where("sport", $sport)->where("tid", $lid)->first();
        if (!isset($tag)) {
            return null;
        }

        $tag_id = $tag->id;
        $query = HotVideo::query();
        $query->whereExists(function ($existsQuery) use ($tag_id) {
            $existsQuery->selectRaw("1");
            $existsQuery->from("tag_relations");
            $existsQuery->where("tag_relations.tag_id", $tag_id);
            $existsQuery->where("tag_relations.type", TagRelation::kTypeVideo);
            $existsQuery->whereRaw("tag_relations.source_id = hot_videos.id");
        });
        $query->where("hot_videos.show", HotVideo::kShow);
        $query->orderByDesc('updated_at');
        $page = $query->paginate($pageSize, ['*'], null, $pageNo);

        $videos = $page->items();
        $array = [];
        $array["sport"] = $sport;
        $array["tags"] = Tag::leagueTags(TagRelation::kTypeVideo, $sport);
        $array['page'] = ['curPage'=>$page->currentPage(), 'total'=>$page->total(), 'pageSize'=>$pageSize, 'lastPage'=>$page->lastPage()];
        foreach ($videos as $video) {
            $array['videos'][] = self::hotVideo2Array($video);
        }
        return $array;
    }

    /**
     * 获取录像列表
     * @param $sport
     * @param $tag_id
     * @param $pageNo
     * @return array|mixed
     */
    protected function getVideosByTag($sport, $tag_id, $pageNo) {
        $pageSize = self::page_size;

        $query = HotVideo::query();
        $query->whereExists(function ($existsQuery) use ($tag_id) {
            $existsQuery->selectRaw("1");
            $existsQuery->from("tag_relations");
            $existsQuery->where("tag_relations.tag_id", $tag_id);
            $existsQuery->where("tag_relations.type", TagRelation::kTypeVideo);
            $existsQuery->whereRaw("tag_relations.source_id = hot_videos.id");
        });
        $query->where("hot_videos.show", HotVideo::kShow);
        $query->orderByDesc('updated_at');
        $page = $query->paginate($pageSize, ['*'], null, $pageNo);
        $videos = $page->items();
        $array = [];
        $array["sport"] = $sport;
        $array["stars"] = Tag::starTags(TagRelation::kTypeVideo, $sport);
        $array['page'] = ['curPage'=>$page->currentPage(), 'total'=>$page->total(), 'pageSize'=>$pageSize, 'lastPage'=>$page->lastPage()];
        foreach ($videos as $video) {
            $array['videos'][] = self::hotVideo2Array($video);
        }
        return $array;
    }

    //=====================================数据接口 结束=====================================//



    //=====================================静态化 开始=====================================//

    public function staticVideoDetail(Request $request, $id) {
        $video = HotVideo::query()->find($id);
        if (isset($video)) {
            $html = $this->videoDetailHtml($video);
            if (!empty($html)) {
                $path = "www" . HotVideo::getVideoDetailPath($id);
                Storage::disk("public")->put($path, $html);
            }
        } else {
            echo "视频不存在<br/>";
        }
    }

    /**
     * 静态化录像列表
     * @param Request $request
     * @param $type
     * @param $page
     */
    public function staticVideosHtml(Request $request, $type, $page) {
        $data = $this->getVideos($type, $page);
        $patch = '/live/videos/' . $type . '/' . $page . '.html';
        if (!isset($data['videos']) || !isset($data['page'])) {
            Storage::delete('public/' . $patch);
            return;
        }
        $types = $this->getTypes();
        $html = $this->videosHtml($type, $types, $data);
        if (!empty($html)) {
            Storage::disk("public")->put($patch, $html);//静态化热门录像分页列表
        }
        $videos = $data['videos'];
        foreach ($videos as $video) {
            $vid = $video['id'];
            $cover = $video['cover'];
            $cover = str_replace('https://www.liaogou168.com', '', $cover);
            $cover = str_replace('http://www.liaogou168.com', '', $cover);
            $video['cover'] = $cover;
            $vJsonStr = json_encode($video);
            if (!empty($vJsonStr)) {
                $patch = MatchTool::hotVideoJsonLink($vid);
                Storage::disk("public")->put($patch, $vJsonStr);//静态化热门录像终端json
            }
        }
    }

    //=====================================静态化 结束=====================================//


    /**
     * 热门录像转化为数组
     * @param $video
     * @return array
     */
    public static function hotVideo2Array($video) {
        $array = ['id'=>$video->id, 'title'=>$video->title, 'aname'=>'', 'lname'=>'录像', 'cover'=>$video->image];
        $array['platform'] = $video->platform;
        $array['player'] = $video->player;
        $array['playurl'] = $video->link;
        $array['code'] = 0;
        return $array;
    }

    protected function pageUrl($type) {
        return "/video/".$type."_page.html";
    }

}