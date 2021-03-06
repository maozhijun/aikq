<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/4/12
 * Time: 15:29
 */

namespace App\Http\Controllers\PC\Live;


use App\Http\Controllers\PC\CommonTool;
use App\Http\Controllers\PC\MatchTool;
use App\Models\LgMatch\BasketScore;
use App\Models\LgMatch\BasketSeason;
use App\Models\LgMatch\BasketTeam;
use App\Models\LgMatch\Score;
use App\Models\LgMatch\Season;
use App\Models\LgMatch\Team;
use App\Models\Match\HotVideo;
use App\Models\Match\HotVideoType;
use App\Models\Subject\SubjectLeague;
use App\Models\Tag\Tag;
use App\Models\Tag\TagRelation;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{

    const page_size = 20;
    const TYPES = ["new"=>"最新", "basketball"=>"篮球", "football"=>"足球", "basketballstar"=>"篮球球星", "footballstar"=>"足球球星", "other"=>"其他"];
    const TYPE_TITLES = ["new"=>"最新国内外篮球、足球精彩视频、集锦-爱看球直播", "basketball"=>"精彩篮球集锦视频_NBA,CBA精彩集锦视频-爱看球直播",
        "football"=>"精彩足球集锦视频_英超、德甲、法甲、欧冠等精彩集锦视频-爱看球直播", "basketballstar"=>"篮球球星精彩集锦大全_NBA球星视频大全-爱看球直播",
        "footballstar"=>"足球球星精彩集锦大全_英超、德甲、法甲、欧冠等球星视频大全-爱看球直播", "other"=>"最新国内外篮球、足球精彩视频、集锦-爱看球直播"];
    //=====================================页面内容 开始=====================================//

    /**
     * 视频 列表
     * @param Request $request
     * @param $page
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function videosDefault(Request $request, $page = 1) {
        $types = ["new"=>"最新", "basketball"=>"篮球", "football"=>"足球", "basketballstar"=>"篮球球星", "footballstar"=>"足球球星", "other"=>"其他"];
        $type = "new";
        $videos = $this->getVideos($type, $page);
        return $this->videosHtml($type, $types, $videos);
    }

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
            $videos = $this->getVideosByLeague($sl, $page);
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
            $videos["videos"] = [];
        }
        $result['types'] = $types;
        $result['page'] = $videos['page'];
        //$result["pageUrl"] = $this->pageUrl($type);
        $result['videos'] = $videos['videos'];
        $result["tags"] = isset($videos["tags"]) ? $videos["tags"] : null;
        $result["stars"] = isset($videos["stars"]) ? $videos["stars"] : null;
        $result["sport"] = isset($videos["sport"]) ? $videos["sport"] : null;
        $result['type'] = $type;
        $result['check'] = 'videos';
        $result["title"] = isset($videos["title"]) ? $videos["title"] : null;
        if (array_key_exists($type,\App\Http\Controllers\Controller::SUBJECT_NAME_IDS)){
            $bj = \App\Http\Controllers\Controller::SUBJECT_NAME_IDS[$type];
            $bj['name_en'] = $type;
            $result['zhuanti'] = $bj;
            //球队
            //球队
            if ($type == 'nba'){
                $season = BasketSeason::where('lid',$bj['lid'])
                    ->orderby('name','desc')->first();
                if (isset($season)){
                    $season = $season['name'];
                }
                $o_score = BasketScore::where('lid',$bj['lid'])
                    ->orderby('rank','asc')
                    ->where('season',$season)
                    ->get();
                $west = array();
                $east = array();
                $tids = array();
                foreach ($o_score as $item){
                    $tids[] = $item['tid'];
                    if ($item['zone'] == 0){
                        $west[] = $item['tid'];
                    }
                    else{
                        $east[] = $item['tid'];
                    }
                }
                $o_teams = BasketTeam::whereIn('id',$tids)->get();
                $teams = array();
                foreach ($o_teams as $item){
                    $teams[$item['id']] = $item;
                }
                $result['teamsData'] = $teams;
                $result['teams'] = array('west'=>$west,'east'=>$east);
            }
            else if ($type == 'cba'){
                $season = BasketSeason::where('lid',$bj['lid'])
                    ->orderby('name','desc')->first();
                if (isset($season)){
                    $season = $season['name'];
                }
                $o_score = BasketScore::where('lid',$bj['lid'])
                    ->orderby('rank','asc')
                    ->where('season',$season)
                    ->get();
                $tids = array();
                foreach ($o_score as $item){
                    $tids[] = $item['tid'];
                }
                $o_teams = \App\Models\Match\BasketTeam::whereIn('id',$tids)->get();
                $teams = array();
                foreach ($o_teams as $item){
                    $teams[$item['id']] = $item;
                }
                $result['teamsData'] = $teams;
                $result['teams'] = $tids;
            }
            else{
                $season = Season::where('lid',$bj['lid'])
                    ->orderby('name','desc')->first();
                if (isset($season)){
                    $season = $season['name'];
                }
                $o_score = Score::where('lid',$bj['lid'])
                    ->where('kind',null)
                    ->where('season',$season)
                    ->orderby('score','desc')
                    ->get();
                $tids = array();
                foreach ($o_score as $item){
                    $tids[] = $item['tid'];
                }
                $o_teams = \App\Models\Match\Team::whereIn('id',$tids)->get();
                $teams = array();
                foreach ($o_teams as $item){
                    $teams[$item['id']] = $item;
                }
                $result['teamsData'] = $teams;
                $result['teams'] = $tids;
            }
            return view('pc.video.league_list', $result);
        }
        else{
            return view('pc.video.list', $result);
        }
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
                $result["def"] = $sl;
            }
        }

        try {
            $comboData = CommonTool::getComboData($name_en);//Storage::get("/public/static/json/pc/comboData/".$name_en.".json");
            $result["comboData"] = $comboData;
        } catch (\Exception $exception) {
        }
        $keywords = $video->htmlKeywords();
        $result["title"] = $video["title"];
        $result["keywords"] = str_replace("，", ",", $keywords);
        $result['check'] = 'videos';
        return view('pc.video.detail', $result);
    }


    public function player(Request $request) {

        return view("pc.video.player");
    }

    public function playerJson(Request $request, $id) {
        $array = "";
        $video = HotVideo::query()->find($id);
        if (!isset($video)) {
            return response()->json(["code"=>-1]);
        }
        $array = ["code"=>0, "id"=>$id, "playurl"=>$video->link, "player"=>$video->player, "platform"=>$video->platform];
        return response()->json($array);
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
        //$query = HotVideo::query();
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
//                $query->whereExists(function ($existsQuery) use ($tag_id) {
//                    $existsQuery->selectRaw("1");
//                    $existsQuery->from("tag_relations");
//                    $existsQuery->where("tag_relations.type", TagRelation::kTypeVideo);
//                    $existsQuery->where("tag_relations.tag_id", $tag_id);
//                    $existsQuery->whereRaw("tag_relations.source_id = hot_videos.id");
//                });
                $page = TagRelation::getRelationsPageById(TagRelation::kTypeVideo, $tag_id, Tag::kLevelTwo, null, $pageNo, $pageSize);
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
//                $query->whereExists(function ($existsQuery) use ($tag_id) {
//                    $existsQuery->selectRaw("1");
//                    $existsQuery->from("tag_relations");
//                    $existsQuery->join("tags", "tags.id", "=", "tag_relations.tag_id");
//                    $existsQuery->where("tags.level", Tag::kLevelFour);
//                    $existsQuery->where("tags.sport", $tag_id);
//                    $existsQuery->where("tag_relations.type", TagRelation::kTypeVideo);
//                    $existsQuery->whereRaw("tag_relations.source_id = hot_videos.id");
//                });
                $page = TagRelation::getRelationsPageById(TagRelation::kTypeVideo, $tag_id, Tag::kLevelFour, null, $pageNo, $pageSize);
                break;
            case "other":
//                $query->whereNotExists(function ($notExistsQuery) {
//                    $notExistsQuery->selectRaw("1");
//                    $notExistsQuery->from("tag_relations");
//                    $notExistsQuery->where("tag_relations.type", TagRelation::kTypeVideo);
//                    $notExistsQuery->whereRaw("tag_relations.source_id = hot_videos.id");
//                });
                $page = TagRelation::getRelationsPageById(TagRelation::kTypeVideo, 3, null, null, $pageNo, $pageSize);
                break;
            default:
                $query = HotVideo::query();
                $query->where("hot_videos.show", HotVideo::kShow);
                $query->orderByDesc('updated_at');
                $page = $query->paginate($pageSize, ['*'], null, $pageNo);
        }

        $videos = $page->items();
        $array['videos'] = [];
        $array['page'] = ['curPage'=>$page->currentPage(), 'total'=>$page->total(), 'pageSize'=>$pageSize, 'lastPage'=>$page->lastPage()];
        foreach ($videos as $video) {
            $array['videos'][] = self::hotVideo2Array($video);
        }
        $titles = self::TYPE_TITLES;
        $array["title"] = isset($titles[$type]) ? $titles[$type] : null;
        return $array;
    }

    /**
     * 获取录像列表
     * @param SubjectLeague $sl
     * @param $pageNo
     * @return array|mixed
     */
    public function getVideosByLeague(SubjectLeague $sl, $pageNo) {
        $pageSize = self::page_size;
        $sport = $sl["sport"];
        $lid = $sl["lid"];
        $name = $sl["name"];
        $tag = Tag::query()->where("sport", $sport)->where("tid", $lid)->first();
        if (!isset($tag)) {
            return null;
        }

        $tag_id = $tag->id;

//        $query = HotVideo::query();
//        $query->whereExists(function ($existsQuery) use ($tag_id) {
//            $existsQuery->selectRaw("1");
//            $existsQuery->from("tag_relations");
//            $existsQuery->where("tag_relations.tag_id", $tag_id);
//            $existsQuery->where("tag_relations.type", TagRelation::kTypeVideo);
//            $existsQuery->whereRaw("tag_relations.source_id = hot_videos.id");
//        });
//        $query->where("hot_videos.show", HotVideo::kShow);
//        $query->orderByDesc('updated_at');
//        $page = $query->paginate($pageSize, ['*'], null, $pageNo);
        $page = TagRelation::getRelationsPageById(TagRelation::kTypeVideo, $sport, null, $tag_id, $pageNo, $pageSize);

        $videos = $page->items();
        $array = [];
        $array["sport"] = $sport;
        $array["tags"] = Tag::leagueTags(TagRelation::kTypeVideo, $sport);
        $array['page'] = ['curPage'=>$page->currentPage(), 'total'=>$page->total(), 'pageSize'=>$pageSize, 'lastPage'=>$page->lastPage()];
        foreach ($videos as $video) {
            $array['videos'][] = self::hotVideo2Array($video);
        }
        $array["title"] = $name."精彩视频大全_".$name."精彩过人视频、投篮视频-爱看球直播";
        return $array;
    }

    /**
     * 获取录像列表
     * @param $sport
     * @param $tag_id
     * @param $pageNo
     * @return array|mixed
     */
    public function getVideosByTag($sport, $tag_id, $pageNo) {
        $pageSize = self::page_size;
        $tag = Tag::query()->find($tag_id);

//        $query = HotVideo::query();
//        $query->whereExists(function ($existsQuery) use ($tag_id) {
//            $existsQuery->selectRaw("1");
//            $existsQuery->from("tag_relations");
//            $existsQuery->where("tag_relations.tag_id", $tag_id);
//            $existsQuery->where("tag_relations.type", TagRelation::kTypeVideo);
//            $existsQuery->whereRaw("tag_relations.source_id = hot_videos.id");
//        });
//
//        $query->where("hot_videos.show", HotVideo::kShow);
//        $query->orderByDesc('updated_at');
//        $page = $query->paginate($pageSize, ['*'], null, $pageNo);

        $page = TagRelation::getRelationsPageById(TagRelation::kTypeVideo, $sport, null, $tag_id, $pageNo, $pageSize);

        $videos = $page->items();
        $array = [];
        $array["sport"] = $sport;
        $array["stars"] = Tag::starTags(TagRelation::kTypeVideo, $sport);
        $array['page'] = ['curPage'=>$page->currentPage(), 'total'=>$page->total(), 'pageSize'=>$pageSize, 'lastPage'=>$page->lastPage()];
        foreach ($videos as $video) {
            $array['videos'][] = self::hotVideo2Array($video);
        }
        if (isset($tag)) {
            $name = $tag["name"];
            if ($sport == 1) {
                $array["title"] = $name."视频集锦大全_".$name."过人视频_".$name."射门视频-爱看球直播";
            } else {
                $array["title"] = $name."视频集锦大全_".$name."过人视频_".$name."投篮视频-爱看球直播";
            }

        }
        return $array;
    }

    //=====================================数据接口 结束=====================================//



    //=====================================静态化 开始=====================================//

    /**
     * 静态化单个录像终端 PC、M 站都一起静态化
     * @param Request $request
     * @param $id
     */
    public function staticVideoDetail(Request $request, $id) {
        $video = HotVideo::query()->find($id);
        if (isset($video)) {
            $videoPath = HotVideo::getVideoDetailPath($id);
            $html = $this->videoDetailHtml($video);
            if (!empty($html)) {
                $path = "www" . $videoPath;
                Storage::disk("public")->put($path, $html);

                $json = ["id"=>$id, "playurl"=>$video->link, "player"=>$video->player, "platform"=>$video->platform];
                $jsonPath = HotVideo::getVideoDetailJsonPath($id);
                Storage::disk("public")->put($jsonPath, json_encode($json));
            }

            $mCon = new \App\Http\Controllers\Mobile\Video\VideoController();
            $mCon->staticVideoDetailHtml($video, $videoPath);
        } else {
            echo "视频不存在<br/>";
        }
    }

    /**
     * 静态化视频播放页面
     * @param Request $request
     */
    public function staticVideoPlayer(Request $request) {
        $html = $this->player($request);
        if (!empty($html)) {
            Storage::disk("public")->put("www/video/player.html", $html);
        }
    }

    /**
     * 批量静态化录像终端
     * @param Request $request
     * @param int $page
     */
    public function staticAllVideoDetail(Request $request, $page = 1) {
        $query = HotVideo::query()->where("show", HotVideo::kShow);
        $videos = $query->paginate(20, ["*"], null, $page);
        foreach ($videos as $video) {
            $id = $video->id;
            $html = $this->videoDetailHtml($video);
            $videoPath = HotVideo::getVideoDetailPath($id);

            if (!empty($html)) {
                //静态化 PC 终端
                $path = "www" . $videoPath;
                Storage::disk("public")->put($path, $html);

                $json = ["id"=>$id, "playurl"=>$video->link, "player"=>$video->player, "platform"=>$video->platform];
                $jsonPath = HotVideo::getVideoDetailJsonPath($id);
                Storage::disk("public")->put($jsonPath, json_encode($json));//静态化 播放 json
            }

            //静态化 M 站终端
            $mCon = new \App\Http\Controllers\Mobile\Video\VideoController();
            $mCon->staticVideoDetailHtml($video, $videoPath);
        }
        echo "curPage = " . $videos->currentPage() . " ， lastPage = " . $videos->lastPage();
    }

    /**
     * 静态化视频列表
     * @param Request $request
     * @param $tab
     * @param $page
     * @return array
     */
    public function staticVideosTabHtml(Request $request, $tab = "", $page = 1) {
        $tabs = HotVideo::getVideoTabs();
        $array = [];
        if (in_array($tab, $tabs)) {
            $videos = $this->getVideos($tab, $page);
            $listPath = HotVideo::getVideoListTabPath($tab, $page);

            //PC端静态化
            $html = $this->videosHtml($tab, self::TYPES, $videos);
            if (!empty($html)) {
                $path = "www" . $listPath;
                $array["path"] = $path;
                Storage::disk("public")->put($path, $html);
            }

            //M端静态化
            $mCon = new \App\Http\Controllers\Mobile\Video\VideoController();
            $mCon->staticVideosHtml($tab, $videos, $listPath);
            $array["m_path"] = "m".$listPath;

            if (isset($videos["page"])) {
                $videoPage = $videos["page"];
                $array["curPage"] = $videoPage["curPage"];
                $array["lastPage"] = $videoPage["lastPage"];
            }
        }
        return $array;
    }

    /**
     * 静态化视频
     * @param Request $request
     * @param $name_en  专题英文缩写
     * @param $page = 1 页码
     * @return array
     */
    public function staticVideosLeagueHtml(Request $request, $name_en, $page = 1) {
        $sl = SubjectLeague::getSubjectLeagueByEn($name_en);
        $array = [];
        if (isset($sl)) {
            $types = ["new"=>"最新", "basketball"=>"篮球", "football"=>"足球", "basketballstar"=>"篮球球星", "footballstar"=>"足球球星", "other"=>"其他"];
            $videos = $this->getVideosByLeague($sl, $page);
            $html = $this->videosHtml($name_en, $types, $videos);
            $leaguePath = HotVideo::getVideoListLeaguePath($name_en, $page);

            if (!empty($html)) {
                $path = "www" . $leaguePath;
                $array["path"] = $path;
                Storage::disk("public")->put($path, $html);
            }

            //M端静态化 m站没有league的页面，直接跳到专题页
            //$mCon = new \App\Http\Controllers\Mobile\Video\VideoController();
            //$mCon->staticVideosHtml($name_en, $videos, $leaguePath);
            //$array["m_path"] = "m".$leaguePath;

            if (isset($videos["page"])) {
                $videoPage = $videos["page"];
                $array["curPage"] = $videoPage["curPage"];
                $array["lastPage"] = $videoPage["lastPage"];
            }
        }
        return $array;
    }

    /**
     *
     * @param Request $request
     * @param $tagId
     * @param $sport
     * @param int $page
     * @return array
     */
    public function staticVideosTagHtml(Request $request, $tagId, $sport, $page = 1) {
        $types = ["new"=>"最新", "basketball"=>"篮球", "football"=>"足球", "basketballstar"=>"篮球球星", "footballstar"=>"足球球星", "other"=>"其他"];
        $videos = $this->getVideosByTag($sport, $tagId, $page);
        $html = $this->videosHtml($tagId, $types, $videos);
        $array = [];
        $tagPath = HotVideo::getVideoListTagPath($sport, $tagId, $page);
        if (!empty($html)) {
            $path = "www" . $tagPath;
            $array["path"] = $path;
            Storage::disk("public")->put($path, $html);
        }

        //M端静态化
        $mCon = new \App\Http\Controllers\Mobile\Video\VideoController();
        $mCon->staticVideosHtml($tagId, $videos, $tagPath);
        $array["m_path"] = "m".$tagPath;

        if (isset($videos["page"])) {
            $videoPage = $videos["page"];
            $array["curPage"] = $videoPage["curPage"];
            $array["lastPage"] = $videoPage["lastPage"];
        }
        return $array;
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
        if ($type == "new" || $type == "") {
            return "/video_page.html";
        }
        if (!in_array($type, self::TYPES)) {

        }
        return "/video/".$type."_page.html";
    }

}