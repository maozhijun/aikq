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
use App\Models\HCT\ForeignArticleCh;
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
        $en_m = ForeignArticleEn::where('url','=',$url)->get();
        if (count($en_m) > 0){
            return response()->json(array('code'=>'0','message'=>'url is exite'));
        }
        else{
            $result = DB::transaction(function () use ($request, $url, $t_en, $t_ch,$c_en,$c_ch) {
                $en_m = new ForeignArticleEn();
                $en_m->url = $url;
                $en_m->title = $t_en;
                $en_m->content = $c_en;
                if ($en_m->save()){
                    $ch_m = new ForeignArticleCh();
                    $ch_m->url = $url;
                    $ch_m->title = $t_ch;
                    $ch_m->content = $c_ch;
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