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
    Route::any("/", "LiveController@lives");
    Route::get('/index.html',"LiveController@lives");//首页

    //直播终端改为在最路由最底部

    Route::get('/live/player.html',"LiveController@player");//播放器
    Route::get('/live/player/player-{cid}-{type}.html',"LiveController@player");//播放器 静态化
    Route::get('/live/iframe/player-{cid}-{type}.html',"LiveController@player");//播放器 静态化 （外链专用）

    Route::get('/live/match_player.html',"LiveController@matchPlayer");//比赛播放器
    Route::get('/live/spPlayer/match_channel-{mid}-{sport}.html',"LiveController@matchPlayerChannel");//比赛播放器 静态化
    Route::get('/live/spPlayer/player-{mid}-{sport}.html',"LiveController@matchPlayerChannel");//比赛播放器 静态化

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

    //播放失败
    Route::any('/live/url_error',"LiveController@liveError");

    //========================================================商务合作 开始========================================================//
    Route::get('/live/business.html', 'LiveController@business');
    //========================================================商务合作 结束========================================================//

    //============================================================================================================================//
    //直播相关静态化
    Route::get('/live/player-json/{id}', 'LiveController@staticLiveUrl');//静态化 线路json
    Route::get('/live/cache/live-json', 'LiveController@allLiveJsonStatic');//直播赛事接口静态化
    Route::get('/live/cache/match/detail_id/{id}/{sport}', 'LiveController@staticLiveDetailById');//静态化wap/pc终端/线路

    //专题静态化
    Route::get('/static/subject/leagues', 'SubjectController@staticSubjectLeagues');//静态化专题列表json
    Route::get('/static/subject/detail/{slid}', 'SubjectController@staticSubjectHtml');//静态化专题终端

    Route::get('/static/subject/videos/detail/{type}/{page}', 'SubjectController@staticSubjectVideoHtmlFromVideos');//静态化列表中所有录像终端页面静态化
    Route::get('/static/subject/video/channel/{ch_id}', 'SubjectController@staticSubjectVideoChannelJson');//专题录像线路静态化
    Route::get('/static/subject/specimen/{sid}', 'SubjectController@staticSubjectSpecimenHtml');//专题集锦html静态化，线路静态化

    Route::get('/static/subject/player', 'SubjectController@staticPlayer');//专题player页面静态化
    Route::get('/live/subject/player.html', 'SubjectController@subjectPlayer');//专题player页面静态化

    //专题录像静态化 开始
    Route::get('/static/subject-videos/leagues', 'SubjectVideoController@staticVideoLeaguesJson');//静态化热门录像类型json
    Route::get('/static/subject-videos/detail/{type}/{page}', 'SubjectVideoController@staticSubjectVideosHtml');//静态化热门录像类型json
    //专题录像静态化 结束

    //热门录像静态化 开始
    Route::get('/static/videos/types', 'VideoController@staticVideoTypesJson');//静态化热门录像类型json
    Route::get('/static/videos/detail', 'VideoController@staticVideoDetail');//静态化热门录像类型json
    Route::get('/static/videos/page/{type}/{page}', 'VideoController@staticVideosHtml');//静态化热门录像 分页列表/终端json
    //热门录像静态化 结束

});

//邀请注册
Route::group(["namespace" => 'Live'],function (){
    Route::get('/live/all/json', 'LiveController@allLiveJsonStatic');
    Route::post('/live/valid/code', 'LiveController@validCode');//验证高清验证码
    Route::get('/live/rec-code/{code}', 'LiveController@recCode');//接收验证码
    Route::get('/live/ad/images', 'LiveController@getVideoAdImage');//获取播放器广告图片
    Route::post('/live/ad/set-active', 'LiveController@setActive');//设置播放器活动
    //Route::get('/invitation/{code}',"HomeController@invitation");
});

Route::group(["namespace" => 'Data'], function () {
    Route::get("/data/", "DataController@index");
    Route::get("/{subject}/data/", "DataController@detail");
    //静态化

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
    Route::get("/app/v101/config.json", "HomeController@appConfig");
    Route::get("/app/v101/config.json", "HomeController@appConfig");
    Route::get("/app/v101/lives/{sport}/{mid}.json", 'Live\LiveController@appLiveDetail');
});

/**
 * 文章
 */
Route::group(["namespace" => 'Article'], function () {
    Route::get("/news", "ArticleController@news");//文章列表
    Route::get("/news/index.html", "ArticleController@news");//文章列表
    Route::get("/news/index{page}.html", "ArticleController@news");//文章列表 分页

    Route::get("/news/lives.html", "ArticleController@detailLives");//终端页直播栏

    Route::get("/news/{param}.html", "ArticleController@detail");//文章终端
    Route::get("/{name_en}/news{id}.html", "ArticleController@detailByName");//文章终端 和上面的文章终端一样，适配不同的URL

    Route::get("/static/article/{id}", "ArticleController@staticDetailHtml");//静态化文章终端
});

//主播
Route::group(["namespace" => 'Anchor'], function () {
    Route::get("/anchor/", "AnchorController@index");//首页
    Route::get("/anchor/room{room_id}.html", "AnchorController@room");//房间
    Route::get('/anchor/room/player/{room_id}.html',"AnchorController@player");//播放器 静态化

    Route::get('/anchor/room/url/{room_id}.json',"AnchorController@playerUrl");//直播链接
});

//主播静态化
Route::group(["namespace" => 'Anchor'], function () {
    Route::get("/api/static/anchor/room/{room}", "AnchorController@staticRoom");
});

/**
 * 直播终端
 */
Route::group(["namespace" => 'Live'], function () {
    Route::get('/live{param}.html',"LiveController@detail");//足球直播页、篮球直播页、自建赛事直播页
    Route::get('/{name_en}/live{param}.html',"LiveController@detailBySL");//足球直播页、篮球直播页、自建赛事直播页
});

/**
 * 球队终端
 */
Route::group(["namespace" => 'Team'], function () {
    Route::get('/{name_en}/team{id}.html',"TeamController@detail");//球队终端

    Route::get('/json/rank/{sport}/{lid}.html',"TeamController@rank");//球队积分
});

Route::group(["namespace" => 'Live'], function () {
    //========================================================专题页面========================================================//
    Route::get('/live/subject/player.html', 'SubjectController@subjectPlayer');//player播放页面

    Route::get('/{name_en}', 'SubjectController@detail');//专题终端页 英超、中超、等等
    Route::get('/{name_en}/video{vid}.html', 'SubjectController@subjectVideo');//专题录像终端页
//    Route::get('/{name_en}/specimen{sid}.html', 'SubjectController@subjectSpecimen');//专题集锦播放终端页面

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
});

//录像 2.0版本
Route::group(["namespace" => 'Record'], function () {
    Route::get('/{name_en}/record{id}.html', 'RecordController@detail');//player播放页面
});