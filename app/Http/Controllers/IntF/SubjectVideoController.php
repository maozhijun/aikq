<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2017/12/14
 * Time: 下午4:33
 */
namespace App\Http\Controllers\IntF;


use App\Models\Match\MatchLive;
use App\Models\Subject\SubjectLeague;
use App\Models\Subject\SubjectVideo;
use App\Models\Subject\SubjectVideoChannels;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;

/**
 * 录像接口
 * Class VideoController
 * @package App\Http\Controllers\Customer2\Aik
 */
class SubjectVideoController extends Controller
{
    const page_size = 20;

    //所有专题录像  /aik/subjects
    /**
     * 所有录像types
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function subjectVideoTypes(Request $request) {
        $leagues = SubjectLeague::getAllLeagues();
        $array = ['all'=>['name'=>'全部', 'count'=>SubjectVideoChannels::channelsCount('all') ]];
        foreach ($leagues as $league) {
            $slid = $league->id;
            $array[$slid] = ['name'=>$league->name, 'count'=>SubjectVideoChannels::channelsCount($slid)];
        }
        $array[SubjectVideo::kOther] = ['name'=>'其他', 'count'=>SubjectVideoChannels::channelsCount(SubjectVideo::kOther) ];
        return response()->json($array);
    }

    /**
     * 获取专题录像分页信息
     * @param Request $request
     * @param $slid
     * @return \Illuminate\Http\JsonResponse
     */
    public function subjectVideosPage(Request $request, $slid) {
        $isMobile = $request->input('isMobile') == 1;
        $pageSize = $request->input('pageSize', self::page_size);
        if ($slid != 'all') {
            if ($slid == SubjectVideo::kOther) {
                $league = [];
            } else {
                $league = SubjectLeague::query()->find($slid);
                if (!isset($league)) {
                    return response()->json([]);
                }
            }
        }
        $query = $this->getVideoQueryNew($isMobile);

        if (isset($league)) {
            $query->where('subject_videos.s_lid', $slid);
        }
        $page = $query->paginate($pageSize);
        $array = ['curPage'=>$page->currentPage(), 'total'=>$page->total(), 'pageSize'=>$pageSize, 'lastPage'=>$page->lastPage()];
        return response()->json($array);
    }

    /**
     * 获取专题录像分页列表
     * @param Request $request
     * @param $slid 专题类型
     * @return \Illuminate\Http\JsonResponse
     */
    public function subjectVideos(Request $request, $slid) {
        $isMobile = $request->input('isMobile') == 1;
        $pageSize = $request->input('pageSize', self::page_size);
        if ($slid != 'all') {
            if ($slid == SubjectVideo::kOther) {
                $league = [];
            } else {
                $league = SubjectLeague::query()->find($slid);
                if (!isset($league)) {
                    return response()->json([]);
                }
            }
        }
        $query = $this->getVideoQueryNew($isMobile);
        if (isset($league)) {
            $query->where('subject_videos.s_lid', $slid);
        }
        $page = $query->paginate($pageSize);
        $videos = $page->items();

        $array = [];
        $array['page'] = ['curPage'=>$page->currentPage(), 'total'=>$page->total(), 'pageSize'=>$pageSize, 'lastPage'=>$page->lastPage()];
        foreach ($videos as $video) {
            $array['videos'][] = SubjectVideo::video2Array($video, $isMobile);
        }
        return response()->json($array);
    }

    /**
     * 获取录像query
     * @param $isMobile
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function getVideoQueryNew($isMobile = false) {
        $query = SubjectVideo::query();
        if ($isMobile) {
            $joinTable = DB::raw('(select sv_id from subject_video_channels where (platform = 1 or platform = 3) group by sv_id ) as ch');
        } else {
            $joinTable = DB::raw('(select sv_id from subject_video_channels where (platform = 1 or platform = 2) group by sv_id ) as ch');
        }
        $query->join($joinTable, 'ch.sv_id', '=', 'subject_videos.id');
        $query->orderByDesc('subject_videos.time')->orderBy('s_lid')->orderBy('subject_videos.mid');
        return $query;
    }

    /**
     * 获取录像query
     * @param $isMobile
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function getVideoQuery($isMobile = false) {
        $query = SubjectVideoChannels::query();
        $query->join('subject_videos', 'subject_videos.id', '=', 'subject_video_channels.sv_id');
        if ($isMobile) {
            $query->where(function ($orQuery) {
                $orQuery->where('subject_video_channels.platform', MatchLive::kPlatformAll);
                $orQuery->orWhere('subject_video_channels.platform', MatchLive::kPlatformPhone);
            });
        }
        $query->selectRaw('subject_videos.hname, subject_videos.aname, subject_videos.hscore, subject_videos.ascore');
        $query->selectRaw('subject_videos.time, subject_videos.s_lid, subject_videos.mid, subject_videos.s_lid');
        $query->selectRaw('subject_video_channels.*, ifNull(subject_video_channels.od, 999) as ch_od ');
        $query->orderByDesc('subject_videos.time')->orderBy('s_lid')->orderBy('subject_videos.mid');
        $query->orderBy('ch_od')->orderBy('id');
        return $query;
    }

    /**
     * 获取 专题录像终端信息/线路信息
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function subjectVideo(Request $request, $id) {
        $array = [];

        $query = $this->getVideoQuery();
        $query->where('subject_video_channels.id', $id);
        $video = $query->first();

        if (isset($video)) {
            $array = self::subjectVideo2Array($video);
        }
        return response()->json($array);
    }

    /**
     * 获取热门录像封面图
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function subjectVideoImages(Request $request) {
        $time = $request->input('time');
        $query = SubjectVideoChannels::query();
        if (!empty($time)) {
            $query->where('updated_at', '>', date('Y-m-d H:i:s', $time) );
        }
        $query->orderByDesc('updated_at');
        $videos = $query->get();
        $video_array = [];
        foreach ($videos as $video) {
            $video_array[] = $video->cover;
        }
        if (count($videos) > 0) {
            $last = strtotime($videos[0]->updated_at);
        } else {
            $last = '';
        }
        $array = ['covers'=>$video_array, 'last'=>$last];
        return response()->json($array);
    }

    /**
     * 热门录像转化为数组
     * @param $video
     * @return array
     */
    public static function subjectVideo2Array($video) {
        $sl = SubjectLeague::query()->find($video->s_lid);
        //$title = $sl->name . ' ' . $video->hname . ' VS '. $video->aname . ' ' . $video->title;
        $title = $video->title;
        $array = ['id'=>$video->id, 'hname'=>$video->hname, 'aname'=>$video->aname, 'cover'=>$video->cover];
        $array['hscore'] = $video->hscore;
        $array['ascore'] = $video->ascore;
        $array['lname'] = $sl->name;
        $array['sv_id'] = $video->sv_id;
        $array['title'] = $title;
        $array['time'] = strtotime($video->time);
        $array['platform'] = $video->platform;
        $array['player'] = $video->player;
        $array['playurl'] = $video->content;
        $array['code'] = 0;
        return $array;
    }

}