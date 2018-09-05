<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/8/3
 * Time: 16:56
 * 火车头接入
 */

namespace App\Http\Controllers\PC\Article;


use App\Http\Controllers\Controller;
use App\Models\Article\PcArticle;
use App\Models\Article\PcArticleType;
use App\Models\HCT\ForeignArticle;
use App\Models\HCT\ForeignArticleCh;
use App\Models\HCT\ForeignArticleContent;
use App\Models\HCT\ForeignArticleEn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ArticleHCTController extends Controller
{
    public function saveHCT(Request $request){
        $url = $request->input('url');
        $t_en = $request->input('title_en');
        $t_ch = $request->input('title_ch');
        $c_en = $request->input('content_en');
        $c_ch = $request->input('content_ch');
        if (is_null($url)){
            return response()->json(array('code'=>'-1','message'=>'url error'));
        }
        elseif(is_null($t_en)){
            return response()->json(array('code'=>'-1','message'=>'t_en error'));
        }
        elseif(is_null($t_ch)){
            return response()->json(array('code'=>'-1','message'=>'t_en error'));
        }
        elseif(is_null($c_en)){
            return response()->json(array('code'=>'-1','message'=>'t_en error'));
        }
        elseif(is_null($c_ch)){
            return response()->json(array('code'=>'-1','message'=>'t_en error'));
        }
        $en_m = ForeignArticle::where('url','=',$url)->get();
        if (count($en_m) > 0){
            return response()->json(array('code'=>'0','message'=>'url is exite'));
        }
        else{
            $result = DB::transaction(function () use ($request, $url, $t_en, $t_ch,$c_en,$c_ch) {
                $en_m = new ForeignArticle();
                $en_m->url = $url;
                $en_m->title_en = $t_en;
                $en_m->title_ch = $t_ch;
                if ($en_m->save()){
                    $ch_m = new ForeignArticleContent();
                    $ch_m->content_en = $c_en;
                    $ch_m->content_ch = $c_ch;
                    $ch_m->id = $en_m->id;
                    if ($ch_m->save()) {
                        return response()->json(array('code' => '0', 'message' => 'success'));
                    }
                    else{
                        return response()->json(array('code'=>'-1','message'=>'save ch error'));
                    }
                }
                else{
                    return response()->json(array('code'=>'-1','message'=>'save en error'));
                }
            });
            return $result;
        }
    }
}