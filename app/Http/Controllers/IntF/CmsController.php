<?php
/**
 * Created by PhpStorm.
 * User: 11247
 * Date: 2018/9/6
 * Time: 10:22
 */

namespace App\Http\Controllers\IntF;


use App\Http\Controllers\Controller;
use App\Models\Match\MatchLive;
use Illuminate\Http\Request;

class CmsController extends Controller
{

    public function getChannels(Request $request, $mid, $sport, $isMobile = false) {
        $mobile = self::isMobile($request) || $request->input('mobile') == 1 || $isMobile;
        $query = MatchLive::query()->where('match_id', $mid);
        $query->where('sport', $sport);
        $matchLive = $query->first();
        if (!isset($matchLive)) {
            return response()->json(['code'=>0, 'channels'=>[] ]);
        }
        if ($mobile) {
            $channels = $matchLive->mChannels();
        } else {
            $channels = $matchLive->kChannels();
        }
        return response()->json(['code'=>0, 'channels'=>$channels]);
    }

}