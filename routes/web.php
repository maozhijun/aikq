<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::group([], function () {

    Route::get("/livesError", "KanQiuMaController@livesError");//提交播放错误url的cid
    Route::get("/livesJson", "KanQiuMaController@livesJson");//全部直播列表
    Route::get("/footballLivesJson", "KanQiuMaController@footballLivesJson");//足球直播列表
    Route::get("/basketballLivesJson", "KanQiuMaController@basketballLivesJson");//篮球直播列表
    Route::get("/otherLivesJson", "KanQiuMaController@otherLivesJson");//自建赛事直播列表

    Route::get("/lives/detailJson/{id}", "KanQiuMaController@detailJson");//直播终端
    Route::get("/lives/basketDetailJson/{id}", "KanQiuMaController@basketDetailJson");//篮球直播终端
    Route::get("/lives/selfDetailJson/{id}", "KanQiuMaController@selfDetailJson");//自建赛事直播终端

    Route::get("/lives/liveMatchesJson", "KanQiuMaController@getLiveMatchesJson");//正在直播比赛
    Route::get("/lives/multiLiveJson/{param}", "KanQiuMaController@multiLive");//多屏直播终端
    Route::get("/lives/multiLiveDivJson/{param}", "KanQiuMaController@multiVideoDiv");//多屏直播终端div
    Route::get("/lives/multiBasketLiveJson/{param}", "KanQiuMaController@multiBasketLive");//篮球多屏直播终端
    Route::get("/lives/multiBasketLiveDivJson/{param}", "KanQiuMaController@multiBasketVideoDiv");//篮球多屏直播终端div

});

//爱球接口
Route::group([], function () {
    Route::get("/aik/livesError", "AikanQController@livesError");//提交播放错误url的cid


    Route::get("/aik/livesJson", "AikanQController@livesJson");//全部直播列表
    Route::get("/aik/footballLivesJson", "AikanQController@footballLivesJson");//足球直播列表
    Route::get("/aik/basketballLivesJson", "AikanQController@basketballLivesJson");//篮球直播列表
    Route::get("/aik/otherLivesJson", "AikanQController@otherLivesJson");//自建赛事 直播列表

    //=================================================================================================================================//
    Route::get("/aik/lives/detailJson/{id}", "AikanQController@detailJson");//直播终端
    Route::get("/aik/lives/basketDetailJson/{id}", "AikanQController@basketDetailJson");//篮球直播终端
    Route::get("/aik/lives/otherDetailJson/{id}", "AikanQController@otherDetailJson");//自建赛事直播终端

    Route::get("/aik/lives/detailJson/{id}.json", "AikanQController@detailJson");//直播终端
    Route::get("/aik/lives/basketDetailJson/{id}.json", "AikanQController@basketDetailJson");//篮球直播终端
    Route::get("/aik/lives/otherDetailJson/{id}.json", "AikanQController@otherDetailJson");//自建赛事直播终端

    Route::get("/aik/lives/detailJson/mobile/{id}.json", "AikanQController@mobileDetailJson");//直播终端
    Route::get("/aik/lives/basketDetailJson/mobile/{id}.json", "AikanQController@mobileBasketDetailJson");//篮球直播终端
    Route::get("/aik/lives/otherDetailJson/mobile/{id}.json", "AikanQController@mobileOtherDetailJson");//自建赛事直播终端
    //=================================================================================================================================//


    Route::get("/aik/lives/liveMatchesJson", "AikanQController@getLiveMatchesJson");//正在直播比赛

    Route::get("/match/live/url/channel/{id}", "AikanQController@getLiveUrl");//正在直播比赛
    Route::get("/match/live/url/match/{id}", 'AikanQController@getLiveUrlMatch');//根据live matchid获取直播url

    //===========================专题相关===========================//
    Route::get("/aik/subjects", "AikanQController@subjects");//专题列表
    Route::get("/aik/subjects/covers", 'AikanQController@subjectLeaguesImages');//专题icon列表
    Route::get("/aik/subjects/detail/{slid}", "AikanQController@subjectDetail");//专题终端
    Route::get("/aik/subjects/carousel/{slid}", "AikanQController@subjectCarousel");//专题终端焦点图,暂时只有世界杯用
    Route::get("/aik/subjects/video/{vid}", "AikanQController@subjectVideo");//专题录像播放终端
    Route::get("/aik/subjects/specimen/{sid}", "AikanQController@subjectSpecimen");//专题录像播放终端

    Route::get("/aik/subjects/video/channel/{cid}", "AikanQController@subjectVideoChannelJson");//专题录像播放终端
    Route::get("/aik/subjects/specimen/channel/{cid}", "AikanQController@subjectSpecimenChannelJson");//专题录像播放终端

    //--------------------------------------------------------------------//
    Route::get("/aik/subjects/league/video/leagues", "SubjectVideoController@subjectVideoTypes");//专题录像列表 信息
    Route::get("/aik/subjects/league/video/page-msg/{slid}", "SubjectVideoController@subjectVideosPage");//专题录像列表分页信息
    Route::get("/aik/subjects/league/video/page/{slid}", "SubjectVideoController@subjectVideos");//专题录像列表分页
    Route::get("/aik/subjects/league/video/covers", "SubjectVideoController@subjectVideoImages");//专题录像列表信息
    Route::get("/aik/subjects/league/video/detail/{slid}", "SubjectVideoController@subjectVideo");//专题录像列表信息
    //===========================专题相关===========================//


    //===========================世界杯相关===========================//
    //Route::get('/aik/worldcup/hotMatch', 'AikanQController@fifaHotMatch');//热门比赛
    //淘汰赛赛程
    //Route::get('/aik/worldcup/schedule', 'AikanQController@getFIFASchedule');
});


Route::group([], function () {
    Route::get("/db/spread/matchList.html", "DongQiuZhiBoController@matchList");
    Route::get("/spread/api/matchList.json", "DongQiuZhiBoController@matchListJson");
});


Route::group(['middleware' => ['web']], function () {//, 'wx_auth', 'wx_base'        //$user = session('wechat.oauth_user'); // 拿到授权用户资料
    Route::get('/act/transfer.html', function () {
        return view('transfer.index');
    });
});
Route::get('/act/transfer/rank.html', "TransferController@rank");