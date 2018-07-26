<?php
/**
 * Created by PhpStorm.
 * User: BJ
 * Date: 2018/7/13
 * Time: 下午3:54
 */
namespace App\Http\Controllers\Admin\Anchor;

use App\Http\Controllers\Admin\UploadTrait;
use App\Models\Anchor\Anchor;
use App\Models\Anchor\AnchorRoom;
use App\Models\Anchor\AnchorRoomTag;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class AnchorRoomController extends Controller
{
    use UploadTrait;

    /**
     * 主播房间列表正在直播
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function living_rooms(Request $request){
        $query = AnchorRoom::query();
        $query->where('status',AnchorRoom::kStatusLiving);
        $query->orderByDesc('created_at');
        $page = $query->paginate(15);

        $result['page'] = $page;
        return view('admin.anchor.anchor_room_living_list', $result);
    }

    /**
     * 主播房间列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function rooms(Request $request){
        $query = AnchorRoom::query();
        $query->orderByDesc('created_at');
        $page = $query->paginate(15);

        $result['page'] = $page;
        return view('admin.anchor.anchor_room_list', $result);
    }

    /**
     * 更新主播房间信息
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function update(Request $request){
        if ($request->input('id',0) == 0){
            return back()->with('error', 'id不能为空');
        }
        $anchor = AnchorRoom::find($request->input('id'));
        if (is_null($anchor)){
            return back()->with('error', '找不到该房间');
        }
        $anchor->title = $request->input('name');
        if ($request->hasFile("cover")) {
            $icon = $this->saveUploadedFile($request->file("cover"), 'cover');
            $anchor->cover = $icon->getUrl();
        }
        $anchor->status = $request->input('status');
        if ($anchor->save()){
            return back()->with('success', '保存成功');
        }
        else{
            return back()->with('error', '保存失败');
        }
    }

    /**
     * 预约列表
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function bookList(Request $request){
        $tags = AnchorRoomTag::query()
            ->where('match_time','>',date_create('-4 hours'))
            ->paginate(15);

        $result['page'] = $tags;
        return view('admin.anchor.book_list',$result);
    }

    /**
     * 修改预约信息
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function bookUpdate(Request $request){
        if ($request->input('id',0) == 0){
            return back()->with('error', 'id不能为空');
        }
        $tag = AnchorRoomTag::find($request->input('id'));
        if (is_null($tag)){
            return back()->with('error', '找不到该预约信息');
        }
        $tag->hot = $request->input('hot');
        if ($tag->save()){
            return back()->with('success', '保存成功');
        }
        else{
            return back()->with('error', '保存失败');
        }
    }
}