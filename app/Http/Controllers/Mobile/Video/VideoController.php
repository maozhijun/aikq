<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2019/3/18
 * Time: 12:16
 */

namespace App\Http\Controllers\Mobile\Video;


use App\Http\Controllers\Controller;
use App\Http\Controllers\PC\CommonTool;
use App\Models\LgMatch\BasketScore;
use App\Models\LgMatch\BasketSeason;
use App\Models\LgMatch\BasketTeam;
use App\Models\LgMatch\Score;
use App\Models\LgMatch\Season;
use App\Models\Match\HotVideo;
use App\Models\Match\Team;
use App\Models\Subject\SubjectLeague;
use App\Models\Tag\Tag;
use App\Models\Tag\TagRelation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class VideoController extends Controller
{

    /**
     * 录像列表
     * @param Request $request
     * @param $page 1 页码
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function videosDefault(Request $request, $page = 1) {
        $type = "new";
        $pcCon = new \App\Http\Controllers\PC\Live\VideoController();
        $videos = $pcCon->getVideos($type, $page);
        return $this->videosHtml($type, $videos);
    }


    public function videos(Request $request, $type = "new", $page = 1) {
        $pcCon = new \App\Http\Controllers\PC\Live\VideoController();
        $videos = $pcCon->getVideos($type, $page);
        return $this->videosHtml($type, $videos);
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
        $pcCon = new \App\Http\Controllers\PC\Live\VideoController();
        $videos = $pcCon->getVideosByTag(Tag::kSportFootball, $id, $page);
        return $this->videosHtml($id, $videos);
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
        $pcCon = new \App\Http\Controllers\PC\Live\VideoController();
        $videos = $pcCon->getVideosByTag(Tag::kSportBasketball, $id, $page);
        return $this->videosHtml($id, $videos);
    }

    /**
     * 视频终端
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
     * m站分页json
     * @param Request $request
     * @return array
     */
    public function videosJson(Request $request) {

        return [];
    }

    /**
     * 视频终端，专题
     * @param Request $request
     * @param $name_en
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    public function videoDetailByNameEn(Request $request, $name_en, $id) {
        $id = intval($id);
        $video = HotVideo::query()->find($id);
        if (!isset($video)) return "";
        return $this->videoDetailHtml($video);
    }



    /**
     * 录像列表
     * @param $type
     * @param $videos
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View|string
     */
    protected function videosHtml($type, $videos) {
        if (!isset($videos['videos'])) {
            return "";
        }
        $types = \App\Http\Controllers\PC\Live\VideoController::TYPES;
        $result['types'] = $types;
        $result['page'] = $videos['page'];
        $result['videos'] = $videos['videos'];
        $result["tags"] = isset($videos["tags"]) ? $videos["tags"] : null;
        $result["stars"] = isset($videos["stars"]) ? $videos["stars"] : null;
        $result["sport"] = isset($videos["sport"]) ? $videos["sport"] : null;
        $result['type'] = $type;
        $result['check'] = 'videos';
        $result["title"] = isset($videos["title"]) ? $videos["title"] : null;
        return view("mobile.video.lives", $result);
    }

    /**
     * 录像终端 html
     * @param HotVideo $video
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function videoDetailHtml(HotVideo $video) {
        $result["video"] = $video;
        $id = $video["id"];

        //根据视频ID获取 赛事
        $tagQuery = TagRelation::query()->where("tag_relations.type", TagRelation::kTypeVideo);
        $tagQuery->join("tags", "tags.id", "=", "tag_relations.tag_id");
        $tagQuery->where("tags.level", Tag::kLevelTwo);
        $tagQuery->where("tag_relations.source_id", $id);
        $tag = $tagQuery->first();

        $name_en = "all";
        if (isset($tag)) {
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
        $keywords = $video->tagsCn();
        $result["title"] = $video["title"];
        $result["keywords"] = str_replace("，", ",", $keywords);
        $result['check'] = 'videos';
        $result["name_en"] = $name_en;
        return view('mobile.video.detail', $result);
    }


    //=======================================静态化 开始=======================================//

    /**
     * （http调用）单个M站 视频终端 静态化
     * @param Request $request
     * @param $id
     * @return array
     */
    public function staticVideoDetail(Request $request, $id) {
        $hotVideo = HotVideo::query()->find($id);
        if (!isset($hotVideo)) {
            return ["message"=>"静态化失败，没有视频", "code"=>-1];
        }
        $path = "m" . HotVideo::getVideoDetailPath($id);
        $this->staticVideoDetailHtml($hotVideo, $path);
    }

    /**
     * 静态化方法
     * @param HotVideo $hotVideo
     * @param $path
     */
    public function staticVideoDetailHtml(HotVideo $hotVideo, $path) {
        $html = $this->videoDetailHtml($hotVideo);
        if (!empty($html)) {
            $path = "m" . $path;
            Storage::disk("public")->put($path, $html);
        }
    }

    //----------------------------------------------------------------------------------

    /**
     * （http调用）M站 篮球、足球、球星等 页面静态化
     * @param Request $request
     * @param string $tab
     * @param int $page
     * @return array
     */
    public function staticVideosTab(Request $request, $tab = "", $page = 1) {
        $tabs = HotVideo::getVideoTabs();
        $array = [];
        if (in_array($tab, $tabs)) {
            $pcCon = new \App\Http\Controllers\PC\Live\VideoController();
            $videos = $pcCon->getVideos($tab, $page);
            $listPath = HotVideo::getVideoListTabPath($tab, $page);
            //M端静态化
            $this->staticVideosHtml($tab, $videos, $listPath);
            if (isset($videos["page"])) {
                $array["path"] = "m".$listPath;
                $videoPage = $videos["page"];
                $array["curPage"] = $videoPage["curPage"];
                $array["lastPage"] = $videoPage["lastPage"];
            }
        }
        return $array;
    }

    /**
     * （http调用）静态化如 NBA、CBA、中超、英超 等视频列表
     * @param Request $request
     * @param $name_en
     * @param int $page
     * @return array
     */
    public function staticVideosLeague(Request $request, $name_en, $page = 1) {
        $sl = SubjectLeague::getSubjectLeagueByEn($name_en);
        $array = [];
        if (isset($sl)) {
            $pcCon = new \App\Http\Controllers\PC\Live\VideoController();
            $videos = $pcCon->getVideosByLeague($sl, $page);
            $leaguePath = HotVideo::getVideoListLeaguePath($name_en, $page);

            $this->staticVideosHtml($name_en, $videos, $leaguePath);
            $array["path"] = "m".$leaguePath;

            if (isset($videos["page"])) {
                $videoPage = $videos["page"];
                $array["curPage"] = $videoPage["curPage"];
                $array["lastPage"] = $videoPage["lastPage"];
            }
        }
        return $array;
    }


    /**
     * （http调用）静态化 球员标签列表
     * @param Request $request
     * @param $tagId
     * @param $sport
     * @param int $page
     * @return array
     */
    public function staticVideosTag(Request $request, $tagId, $sport, $page = 1) {
        $array = [];
        $pcCon = new \App\Http\Controllers\PC\Live\VideoController();
        $videos = $pcCon->getVideosByTag($sport, $tagId, $page);
        $tagPath = HotVideo::getVideoListTagPath($sport, $tagId, $page);
        $this->staticVideosHtml($tagId, $videos, $tagPath);
        $array["path"] = "m".$tagPath;
        if (isset($videos["page"])) {
            $videoPage = $videos["page"];
            $array["curPage"] = $videoPage["curPage"];
            $array["lastPage"] = $videoPage["lastPage"];
        }
        return $array;
    }

    //------------------------------------------------------------------------------------------------------------------

    /**
     * 静态化 视频列表
     * @param $type
     * @param array $videos
     * @param $path
     */
    public function staticVideosHtml($type, array $videos, $path) {
        if (empty($type) || !isset($videos) || empty($path)){
            return;
        }
        $html = $this->videosHtml($type, $videos);
        if (!empty($html)) {
            $path = "m".$path;
            Storage::disk("public")->put($path, $html);
        }
    }


    //-------------------------------------------------------------------------------------

}