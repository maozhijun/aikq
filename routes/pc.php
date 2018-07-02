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

//直播相关
Route::group(["namespace" => 'Live'], function () {
    Route::any("/", function (){
        return redirect('/index.html');
    });
    Route::get('/betting.html', function (){
        return redirect('/index.html');
    });//竞彩
    Route::get('/football.html', function (){
        return redirect('/index.html');
    });//足球
    Route::get('/basketball.html', function (){
        return redirect('/index.html');
    });//篮球

    Route::get('/index.html',"LiveController@lives");//首页
//    Route::get('/betting.html',"LiveController@betLives");//竞彩
//    Route::get('/football.html',"LiveController@footballLives");//足球
//    Route::get('/basketball.html',"LiveController@basketballLives");//篮球

    Route::get('/live/football/{mid}.html',"LiveController@detail");//足球直播页
    Route::get('/live/basketball/{mid}.html',"LiveController@basketDetail");//篮球直播页
    Route::get('/live/other/{mid}.html',"LiveController@otherDetail");//自建赛事直播页

    Route::get('/live/player.html',"LiveController@player");//播放器
    Route::get('/live/player/player-{cid}-{type}.html',"LiveController@player");//播放器 静态化

    Route::get('/live/match_player.html',"LiveController@matchPlayer");//比赛播放器
    Route::get('/live/match_channel.html',"LiveController@matchPlayerChannel");//比赛播放器
    Route::get('/live/spPlayer/match_channel-{mid}-{sport}.html',"LiveController@matchPlayerChannel");//比赛播放器 静态化
    Route::get('/live/spPlayer/player-{mid}-{sport}.html',"LiveController@matchPlayerChannel");//比赛播放器 静态化

//    Route::get('/match/live-multi/{mid}.html',"LiveController@multiLive");//多屏直播页
//    Route::get('/match/live/match-video/{mid}', 'LiveController@multiLiveDiv');//多屏直播添加
//    Route::get('/match/live/basket-match-video/{mid}', 'LiveController@multiBasketLiveDiv');//篮球多屏直播添加
//    Route::get("/live/football/recommend/{mid}.html", "LiveController@getArticleOfFMid");//直播终端足球推荐
//    Route::get("/live/basketball/recommend/{mid}.html", "LiveController@getArticleOfBMid");//直播终端篮球推荐

    //正在直播
    //Route::get('/live/living_match',"LiveController@getLiveMatches");
    //天天直播
    //Route::get("/match/live/url/zb/{id}", 'LiveController@getTTZBLiveUrl');
    //无插件
    //Route::get("/match/live/url/wcj/{id}", 'LiveController@getWCJLiveUrl');

    //channel通用
    Route::get("/match/live/url/channel/mobile/{id}.json", 'LiveController@getLiveUrl');
    Route::get("/match/live/url/channel/{id}.json", 'LiveController@getLiveUrl');
    Route::get("/match/live/url/channel/{id}", 'LiveController@getLiveUrl');
    Route::get("/match/live/url/channel/hd/{ch_id}", 'LiveController@getHLiveUrl');
    //match获取channel通用
    Route::get("/match/live/url/match/{id}", 'LiveController@getLiveUrlMatch');
    Route::get("/match/live/url/match/pc/{mid}_{sport}", 'LiveController@getLiveUrlMatchPC');
    Route::get("/match/live/url/match/m/{mid}_{sport}", 'LiveController@getLiveUrlMatchM');

    //分享
    Route::get("/player.html", 'LiveController@share');

    //Route::get("/cache/player/recommend/{mid}", 'LiveController@deleteStaticHtml');//推荐

    //播放失败
    Route::any('/live/url_error',"LiveController@liveError");

    //Route::get("/tv/{id}.html", 'VideoController@tvDetail');//热门频道终端
    //Route::get("/tv/channel/{id}", 'VideoController@tvChannel');//热门频道线路

    //Route::get('/video/player.html',"VideoController@player");//播放器

    //Route::get("/video/{id}.html", 'VideoController@videoDetail');//热门视频终端
    //Route::get("/video/channel/{id}", 'VideoController@videoChannel');//热门频道线路

    //Route::get('/live/ex-link/{id}', 'LiveController@exLink');//外链跳转
    //========================================================专题页面========================================================//
    Route::get('/live/subject/player.html', 'SubjectController@subjectPlayer');//player播放页面

    Route::get('/live/subject/{s_lid}.html', 'SubjectController@detail');


    Route::get('/live/subject/video/{first}/{second}/{vid}.html', 'SubjectController@subjectVideo');//集锦终端页面
    Route::get('/live/subject/specimen/{first}/{second}/{sid}.html', 'SubjectController@subjectSpecimen');//集锦播放终端页面

    Route::get('/live/subject/video/channel/mobile/{first}/{second}/{id}.json', 'SubjectController@subjectVideoChannelJson');
    Route::get('/live/subject/video/channel/{first}/{second}/{id}.json', 'SubjectController@subjectVideoChannelJson');

    Route::get('/live/subject/specimen/channel/mobile/{first}/{second}/{id}.json', 'SubjectController@subjectSpecimenChannelJson');
    Route::get('/live/subject/specimen/channel/{first}/{second}/{id}.json', 'SubjectController@subjectSpecimenChannelJson');
    //========================================================专题页面========================================================//

    //========================================================专题热门录像 开始========================================================//
    Route::get('/live/subject/videos/{type}/{page}.html', 'SubjectVideoController@videos');//录像列表
    Route::get('/live/subject/videos/detail.html', 'SubjectVideoController@videoDetail');//录像终端
    //========================================================专题热门录像 结束========================================================//

    //========================================================热门录像 开始========================================================//
    Route::get('/live/videos/{type}/{page}.html', 'VideoController@videos');//录像列表
    Route::get('/live/videos/detail.html', 'VideoController@videoDetail');//录像终端
    //========================================================热门录像 结束========================================================//

    //========================================================商务合作 开始========================================================//
    Route::get('/live/business.html', 'LiveController@business');
    //========================================================商务合作 结束========================================================//

    //============================================================================================================================//
    //直播相关静态化
    Route::get('/live/player-json/{id}', 'LiveController@staticLiveUrl');//静态化 线路json
    Route::get('/live/cache/live-json', 'LiveController@allLiveJsonStatic');//直播赛事接口静态化
    Route::get('/live/cache/match/detail', 'LiveController@staticLiveDetail');//静态化当前所有比赛的直播终端
    Route::get('/live/cache/player/json', 'LiveController@staticPlayerJson');//静态化所有当前正在比赛的线路
    Route::get('/live/cache/flush', 'LiveController@flushVideoCache');//刷新缓存文件
    Route::get('/live/cache/match/detail_id/{id}/{sport}', 'LiveController@staticLiveDetailById');//静态化wap/pc终端/线路

    //专题静态化
    Route::get('/static/subject/leagues', 'SubjectController@staticSubjectLeagues');//静态化专题列表json
    Route::get('/static/subject/detail/{slid}', 'SubjectController@staticSubjectHtml');//静态化专题终端

    Route::get('/static/subject/video/{vid}', 'SubjectController@staticSubjectVideoHtml');//专题录像终端静态化
    Route::get('/static/subject/videos/detail/{type}/{page}', 'SubjectController@staticSubjectVideoHtmlFromVideos');//静态化列表中所有录像终端页面静态化
    Route::get('/static/subject/video/channel/{ch_id}', 'SubjectController@staticSubjectVideoChannelJson');//专题录像线路静态化
    Route::get('/static/subject/specimen/{sid}', 'SubjectController@staticSubjectSpecimenHtml');//专题集锦html静态化，线路静态化

    Route::get('/static/subject/player', 'SubjectController@staticPlayer');//专题player页面静态化

    //专题录像静态化 开始
    Route::get('/static/subject-videos/leagues', 'SubjectVideoController@staticVideoLeaguesJson');//静态化热门录像类型json
    Route::get('/static/subject-videos/detail/{type}/{page}', 'SubjectVideoController@staticSubjectVideosHtml');//静态化热门录像类型json
    //专题录像静态化 结束

    //热门录像静态化 开始
    Route::get('/static/videos/types', 'VideoController@staticVideoTypesJson');//静态化热门录像类型json
    Route::get('/static/videos/detail', 'VideoController@staticVideoDetail');//静态化热门录像类型json
    Route::get('/static/videos/page/{type}/{page}', 'VideoController@staticVideosHtml');//静态化热门录像 分页列表/终端json
//    Route::get('/static/videos/detail/{id}', 'VideoController@staticVideoJson');
    //热门录像静态化 结束

});

//邀请注册
Route::group(["namespace" => 'Live'],function (){
    Route::get('/live/all/json', 'LiveController@allLiveJsonStatic');
    Route::post('/live/valid/code', 'LiveController@validCode');//验证高清验证码
    Route::get('/live/rec-code/{code}', 'LiveController@recCode');//接收验证码
    Route::get('/live/ad/get-image', 'LiveController@getImage');//获取远程图片
    Route::get('/live/ad/images', 'LiveController@getVideoAdImage');//获取播放器广告图片
    Route::post('/live/ad/set-active', 'LiveController@setActive');//设置播放器活动
    //Route::get('/invitation/{code}',"HomeController@invitation");
});

Route::group(["namespace" => 'FIFA'], function () {
//世界杯专题
    Route::get("/worldcup/2018/index.html", "WorldCupController@index");
    Route::get("/worldcup/2018/team/{tid}.html", "WorldCupController@teamDetail");
    //静态化
    Route::get("/pc/static/worldcup/index","WorldCupController@staticIndex");
    Route::get("/pc/static/worldcup/team/{tid}","WorldCupController@staticTeamDetail");
});

Route::group([], function () {
//app配置
    Route::get("/app/config.json", "HomeController@appConfig");
});
