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
        $result['type'] = $type;
        $result['check'] = 'videos';
        return view('pc.video.list', $result);
    }

    /**
     * 录像终端
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function videoDetail(Request $request, $id) {
        $video = HotVideo::query()->find($id);
        if (!isset($video)) return "";

        $result["video"] = $video;
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
     * 获取录像类型
     * @return array|mixed
     */
    public function getTypes() {
        try {
            $types = Storage::get('public/static/json/pc/live/videos/types.json');
        } catch (\Exception $exception) {
            $types = "[]";
        }
        $json = json_decode($types, true);
        $json = isset($json) ? $json : [];
        return $json;
    }

    /**
     * 获取录像列表
     * @param $type
     * @param $pageNo
     * @return array|mixed
     */
    public function getVideos($type, $pageNo) {
        $pageSize = self::page_size;
        $query = HotVideo::query();
        switch ($type) {
            case "basketball":
            case "football":
                $tag_id = $type == "basketball" ? Tag::kSportBasketball : Tag::kSportFootball;
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
                $tag_id = $type == "basketballstar" ? Tag::kSportBasketball : Tag::kSportFootball;
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
        $array = [];
        $array['page'] = ['curPage'=>$page->currentPage(), 'total'=>$page->total(), 'pageSize'=>$pageSize, 'lastPage'=>$page->lastPage()];
        foreach ($videos as $video) {
            $array['videos'][] = self::hotVideo2Array($video);
        }
        return $array;
    }

    /**
     * 获取录像终端json
     * @param $id
     * @param $isMobile
     * @return array|mixed
     */
    public function getVideoDetailJsonStr($id, $isMobile = false) {
        $url = env('LIAOGOU_URL')."aik/videos/" . $id . ($isMobile ? '?isMobile=1' : '');
        $server_output = SubjectController::execUrl($url);
        return $server_output;
    }


    /**
     * 获取热门录像分类的分页信息
     * @param $id
     * @param $isMobile
     * @return array|mixed
     */
    public function getVideoPageMsg($id, $isMobile = false) {
        $pageSize = self::page_size;
        if ($id != 'all') {
            $type = HotVideoType::query()->find($id);
            if (!isset($type)) {
                return response()->json([]);
            }
        }
        $query = HotVideo::query();
        if (isset($type)) {
            $query->where('type_id', $id);
        }
        $page = $query->paginate($pageSize);
        $array = ['curPage'=>$page->currentPage(), 'total'=>$page->total(), 'pageSize'=>$pageSize, 'lastPage'=>$page->lastPage()];
        return $array;
    }
    //=====================================数据接口 结束=====================================//



    //=====================================静态化 开始=====================================//

    /**
     * 静态化类型列表json
     * @param Request $request
     */
    public function staticVideoTypesJson(Request $request) {
        $types = HotVideoType::allTypes();
        $array = ['all'=>'全部'];
        foreach ($types as $type) {
            $array[$type->id] = $type->name;
        }
        $typesStr = json_encode($array);
        Storage::disk("public")->put('/static/json/pc/live/videos/types.json', $typesStr);
    }

    /**
     * 静态化类型列表json
     * @param Request $request
     */
    public function staticVideoDetail(Request $request) {
        $html = $this->videoDetail($request);
        Storage::disk("public")->put('/live/videos/detail.html', $html);
    }


    public function syncVideoImages(Request $request) {
        $save_patch = '/live/videos/cover';
        $url = '';
        $ch = curl_init();
        $timeout = 3;
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        $img = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        echo "http_code: " . $http_code . "\n";
        curl_close($ch);
        if ($http_code >= 400) {
            echo "获取链接内容失败";
            return;
        }
        $list = explode("/", $url);
        $ext = $list[count($list) - 1];
        $list = explode('?', $ext);
        $fileName = $list[0];
        $file_patch = $save_patch . $fileName;
        Storage::disk('public')->put($file_patch, $img);

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

    /**
     * 静态化录像终端/线路 信息
     * @param Request $request
     * @param $id
     */
    public function staticVideoJson(Request $request, $id) {
        //live/videos/channel/index/id.json
        $jsonStr = $this->getVideoDetailJsonStr($id);
        if (!empty($jsonStr)) {
            $patch = MatchTool::hotVideoJsonLink($id);
            Storage::disk("public")->put($patch, $jsonStr);
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
//        if ($type == "new") {
//            return "/video_page.html";
//        }
        return "/video/".$type."_page.html";
    }

}