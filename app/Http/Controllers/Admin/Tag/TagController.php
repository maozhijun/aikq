<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2019/2/18
 * Time: 12:56
 */

namespace App\Http\Controllers\Admin\Tag;


use App\Models\LgMatch\BasketScore;
use App\Models\LgMatch\BasketStage;
use App\Models\LgMatch\Score;
use App\Models\Tag\Tag;
use App\Models\Tag\TagRelation;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;

class TagController extends Controller
{

    /**
     * 标签列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function tags(Request $request) {
        $name = $request->input("name");
        $sport = $request->input("sport");
        $level = $request->input("level");

        $query = Tag::query();
        if (!empty($name)) {
            $query->where("name", "like", "%$name%");
        }
        if (is_numeric($sport)) {
            $query->where("sport", $sport);
        }
        if (is_numeric($level)) {
            $query->where("level", $level);
        }
        $pages = $query->paginate();
        $pages->appends($request->all());
        $result["pages"] = $pages;
        return view("admin.tag.tags", $result);
    }

    /**
     * 删除标签
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delTag(Request $request) {
        $id = $request->input("id");
        try {
            Tag::query()->where("id", $id)->delete();
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(["code"=>500, "message"=>"服务器错误"]);
        }
        return response()->json(["code"=>200, "message"=>"删除成功"]);
    }

    /**
     * 添加、修改标签
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function saveTag(Request $request) {
        $id = $request->input("id");
        $name = $request->input("name");
        $sport = $request->input("sport");
        $level = $request->input("level");
        //$tid = $request->input("tid");

        if (empty($name)) {
            return response()->json(["code"=>401, "message"=>"标签名称不能为空"]);
        }

        if (!in_array($sport, [Tag::kSportFootball, Tag::kSportBasketball])) {
            return response()->json(["code"=>401, "message"=>"类型错误"]);
        }

        if (!in_array($level, Tag::kLevelArray)) {
            return response()->json(["code"=>401, "message"=>"级别错误"]);
        }

        try {
            $repeatQuery = Tag::query()->where("sport", $sport)->where("level", $level)->where("name", $name);
            if (is_numeric($id)) {
                $repeatQuery->where("id", "<>", $id);
                $tag = Tag::query()->find($id);
            }
            //判断是否存在重复标签
            if ($repeatQuery->count() > 0) {
                return response()->json(["code"=>403, "message"=>"已存在此标签"]);
            }
            if (!isset($tag)) {
                $tag = new Tag();
            }

            $tid = Tag::findTid($name, $sport, $level);

            $tag->name = $name;
            $tag->sport = $sport;
            $tag->level = $level;
            $tag->tid = $tid;
            $tag->save();
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(["code"=>500, "message"=>"服务器错误"]);
        }

        return response()->json(["code"=>200, "message"=>"保存成功"]);
    }



    /**
     * 查找标签，主要是用于查找赛事标签
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function findTag(Request $request) {
        $sport = $request->input("sport");
        $level = $request->input("level");

        $query = Tag::query();
        if (is_numeric($sport)) {
            $query->where("sport", $sport);
        }
        if (is_numeric($level)) {
            $query->where("level", $level);
        }
        $query->select(["id", "sport", "level", "name", "tid"]);
        $data = $query->get()->toArray();
        return response()->json(["code"=>200, "data"=>$data]);
    }


    /**
     * 根据 赛事ID 查找球队
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function findTeams(Request $request) {
        $sport = $request->input("sport", 1);
        $lid = $request->input("lid");
        if (!is_numeric($lid)) {
            return response()->json(["code"=>-1, "message"=>"参数错误"]);
        }
        $data = [];
        if ($sport == 2) {
            $basketScore = BasketScore::query()->where("lid", $lid)->selectRaw("max(season) as m_season")->first();
            if (isset($basketScore)) {
                $query = BasketScore::query()->where("lid", $lid)->where("season", $basketScore->m_season);
                $query->join("basket_teams", "basket_teams.id", "=", "basket_scores.tid");
                $query->selectRaw("basket_teams.id, basket_teams.name_china as name, basket_scores.lid");
                $query->orderBy("rank");
                $data = $query->get()->toArray();
            }
        } else {
            $score = Score::query()->where("lid", $lid)->selectRaw("max(season) as m_season")->first();
            if (isset($score)) {
                $query = Score::query()->where("lid", $lid)->where("season", $score->m_season);
                $query->whereNull("scores.kind");
                $query->join("teams", "teams.id", "=", "scores.tid");
                $query->selectRaw("teams.id, teams.name, scores.lid");
                $query->orderBy("rank");
                $data = $query->get()->toArray();
            }
        }
        return response()->json(["code"=>200, "data"=>$data]);
    }

    /**
     * 删除关系标签
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delTagRelation(Request $request) {
        $id = $request->input("id");//标签关系ID

        if (!is_numeric($id)) {
            return response()->json(["code"=>401, "message"=>"参数错误"]);
        }

        try {
            TagRelation::query()->where("id", $id)->delete();
        } catch (\Exception $exception) {
            Log::error($exception);
            return response()->json(["code"=>500, "message"=>"服务器错误"]);
        }
        return response()->json(["code"=>200, "message"=>"删除成功"]);
    }

}