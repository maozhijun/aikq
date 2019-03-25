<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2019/2/26
 * Time: 18:21
 */

namespace App\Http\Controllers\Admin\Video;


use App\Console\DeleteExpireFileCommand;
use App\Http\Controllers\Controller;
use App\Http\Controllers\PC\CommonTool;
use App\Http\Controllers\PC\HomeController;
use App\Http\Controllers\PC\StaticController;
use App\Models\Match\HotVideo;
use App\Models\Subject\SubjectLeague;
use App\Models\Tag\Tag;
use App\Models\Tag\TagRelation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class HotVideoController extends Controller
{

    /**
     * 视频
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function hotVideos(Request $request) {
        $title = $request->input("title");
        $sport = $request->input("sport");
        $tag = $request->input("tag");
        $player = $request->input("player");

        $query = HotVideo::query();
        if (!empty($title)) {
            $query->where("hot_videos.title", "like", "%$title%");
        }
        if (is_numeric($player)) {
            $query->where("hot_videos.player", $player);
        }
        if (is_numeric($sport)) {
            $query->whereExists(function ($existsQuery) use ($sport) {
                $existsQuery->selectRaw("1");
                $existsQuery->from("tag_relations");
                $existsQuery->where("tag_relations.tag_id", $sport);
                $existsQuery->where("tag_relations.type", TagRelation::kTypeVideo);
                $existsQuery->whereRaw("tag_relations.source_id = hot_videos.id");
            });
        }

        if (!empty($tag)) {
            $query->whereExists(function ($existsQuery) use ($tag) {
                $existsQuery->selectRaw("1");
                $existsQuery->from("tag_relations");
                $existsQuery->join("tags", "tag_relations.tag_id", "=", "tags.id");
                $existsQuery->where("tags.name", "like", "%$tag%");
                $existsQuery->where("tag_relations.type", TagRelation::kTypeVideo);
                $existsQuery->whereRaw("tag_relations.source_id = hot_videos.id");
            });
        }

        $query->orderBy("hot_videos.show");
        $query->orderBy("hot_videos.created_at", "desc");

        $pages = $query->paginate();

        $result["pages"] = $pages;
        return view("admin.video.list", $result);
    }

    /**
     * 录像编辑页面
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function videoEditPage(Request $request) {
        $id = $request->input("id");
        if (is_numeric($id)) {
            $result["video"] = HotVideo::query()->find($id);
            $array = TagRelation::tagCellArray(TagRelation::kTypeVideo, $id);
            $result = array_merge($result, $array);
        }
        if (!isset($result["sports"])) {
            $result["sports"] = Tag::sports();
        }
        $result["players"] = HotVideo::kPlayerArrayCn;
        $result["platforms"] = HotVideo::kPlatformArray;
        return view("admin.video.edit", $result);
    }

    /**
     * 保存视频
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveHotVideo(Request $request) {
        $adAccount = $request->_account;

        $id = $request->input("id");
        $title = $request->input("title");
        $image = $request->input("image");
        $link = $request->input("link");
        $platform = $request->input("platform");
        $player = $request->input("player");
        $sport = $request->input("sport");
        $labels = $request->input("labels");

        $tags = $request->input("tags");//标签
        //判断参数是否正确
        if (empty(trim($title))) {
            return response()->json(["code"=>401, "message"=>"视频标题不能为空"]);
        }
        if (mb_strlen($title) > 64) {
            return response()->json(["code"=>401, "message"=>"视频标题不能多于64字符"]);
        }
        if (empty(trim($link))) {
            return response()->json(["code"=>401, "message"=>"视频连接不能为空"]);
        }
        if (strlen($link) > 500) {
            return response()->json(["code"=>401, "message"=>"视频连接不能大于500字"]);
        }
        if (empty($image)) {
            return response()->json(["code"=>401, "message"=>"必须上传封面图"]);
        }
        if (empty($platform)) {
            return response()->json(["code"=>401, "message"=>"请选择播放平台"]);
        }
        if (empty($player)) {
            return response()->json(["code"=>401, "message"=>"请选择播放方式"]);
        }
        if (empty($sport)) {
            return response()->json(["code"=>401, "message"=>"请选择竞技"]);
        }

        if (!empty($labels)) {
            $labels = str_replace("，", ",", $labels);
        }

        try {
            if (is_numeric($id)) {
                $video = HotVideo::query()->find($id);
            }
            if (!isset($video)) {
                $video = new HotVideo();
            }
            $video->title = $title;
            $video->link = $link;
            $video->player = $player;
            $video->platform = $platform;
            $video->image = $image;
            $video->ad_id = $adAccount->id;
            $video->labels = $labels;

            DB::transaction(function () use ($video, $tags, $sport) {
                $video->save();
                $tagArray = json_decode($tags, true);
                $tagArray = is_null($tagArray) ? [] : $tagArray;
                TagRelation::saveVideoTagRelation($sport, $video->id, $tagArray);
            });
            HotVideo::staticHotVideoDetailHtml($video->id);//静态化视频终端


            StaticController::staticDetail(TagRelation::kTypeVideo,$video->id);//静态化视频相关页面
            HomeController::updateFileComboData("all");//更新全部，专题的 在 StaticController::staticDetail更新
//            $array = TagRelation::getLeagueTagRelations(TagRelation::kTypeVideo, $video->id);
//            if (isset($array) && count($array) > 0) {//更新combo.json
//                foreach ($array as $item) {
//                    $sport = $item["sport"];
//                    $tid = $item["tid"];
//                    $sl = SubjectLeague::getSubjectLeagueByLid($sport, $tid);
//                    if (isset($sl)) {
//                        HomeController::updateFileComboData($sl["name_en"]);
//                    }
//                }
//            }

            return response()->json(["code"=>200, "message"=>"保存成功", "id"=>$video->id]);
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(["code"=>500, "message"=>"系统错误"]);
        }
    }

    /**
     * 删除视频
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delHotVideo(Request $request) {
        $id = $request->input("id");
        if (!is_numeric($id)) {
            return response()->json(["code"=>403, "message"=>"参数错误"]);
        }
        try {
            DB::transaction(function () use ($id) {
                $path = HotVideo::getVideoDetailPath($id);
                HotVideo::query()->where("id", $id)->delete();
                TagRelation::deleteTagRelations(TagRelation::kTypeVideo, $id);//删除关系标签
                DeleteExpireFileCommand::delStoragePublicFile("www".$path);//删除文件
                DeleteExpireFileCommand::delStoragePublicFile("m".$path);//删除文件
            });
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(["code"=>500, "message"=>"服务器错误"]);
        }
        return response()->json(["code"=>200, "message"=>"删除成功"]);
    }

    /**
     * 显示、隐藏录像
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function displayVideo(Request $request) {
        $id = $request->input("id");
        $type = $request->input("type");//1：显示，2：不显示
        if (!is_numeric($id) || !in_array($type, [1, 2])) {
            return response()->json(["code"=>401, "message"=>"参数错误"]);
        }
        try {
            $video = HotVideo::query()->find($id);
            if (!isset($video)) {
                return response()->json(["code"=>401, "message"=>"视频不存在"]);
            }
            if ($video->show != $type) {
                $video->show = $type;
                $video->save();
            }
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(["code"=>500, "message"=>"服务器错误"]);
        }
        StaticController::staticDetail(TagRelation::kTypeVideo,$video->id);
        return response()->json(["code"=>200, "message"=>"操作成功"]);
    }

}