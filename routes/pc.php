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

    Route::get("/static/data_index/", "DataController@staticIndex");
    Route::get("/static/data_subject/{subject}", "DataController@staticSubject");
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

    Route::get("/app/comboData/{name_en}.json", 'HomeController@updateComboData');
    Route::get('/api/recordData.json', 'Record\RecordController@getMatchWithDate');//接口
});

/**
 * 文章
 */
Route::group(["namespace" => 'Article'], function () {
//    Route::get("/news", "ArticleController@news");//文章列表
//    Route::get("/news/index.html", "ArticleController@news");//文章列表
//    Route::get("/news/index{page}.html", "ArticleController@news");//文章列表 分页
    Route::get("/news", "ArticleController@newsHome");//文章首页
    Route::get("/news/index.html", "ArticleController@newsHome");//文章首页

    Route::get("/news/lives.html", "ArticleController@detailLives");//终端页直播栏

    Route::get("/news/{param}.html", "ArticleController@detail");//文章终端
    Route::get("/{name_en}/news{id}.html", "ArticleController@detailByName");//文章终端 和上面的文章终端一样，适配不同的URL

    Route::get("/{name_en}/news", "ArticleController@subjectNews");//专题文章列表
    Route::get("/{name_en}/news/index.html", "ArticleController@subjectNews");//专题文章列表
    Route::get("/{name_en}/news/index{page}.html", "ArticleController@subjectNews");//专题文章列表

    Route::get("/static/article/{id}", "ArticleController@staticDetailHtml");//静态化文章终端

    //2.0
    Route::get("/static/news/", "ArticleController@recordDetailHtml");//静态化首页
    Route::get("/static/news_subject/{league}/{page}", "ArticleController@subjectDetailHtml");//静态化专题列表
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
    //v2
    Route::get('/{name_en}/team{id}_record_{page}.html',"TeamController@recordDetail");//球队终端 录像
    Route::get('/{name_en}/team{id}_index_{page}.html',"TeamController@detail");//球队终端 首页
    Route::get('/{name_en}/team{id}_news_{page}.html',"TeamController@newsDetail");//球队终端 资讯
    Route::get('/{name_en}/team{id}_video_{page}.html',"TeamController@videoDetail");//球队终端 视频

    Route::get("/static/team_record/{sport}/{name_en}/{tid}/{page}", "TeamController@staticRecordHtml");//球队录像终端静态化
    Route::get("/static/team_index/{sport}/{name_en}/{tid}/{page}", "TeamController@staticIndexHtml");//球队录像终端静态化
    Route::get("/static/team_news/{sport}/{name_en}/{tid}/{page}", "TeamController@staticNewsHtml");//球队录像终端静态化
    Route::get("/static/team_video/{sport}/{name_en}/{tid}/{page}", "TeamController@staticVideoHtml");//球队录像终端静态化

    Route::get('/{name_en}/team{id}.html',"TeamController@detail");//球队终端
    Route::get('/json/rank/{sport}/{lid}.html',"TeamController@rank");//球队积分
});

Route::group(["namespace" => 'Live'], function () {
    //========================================================视频2.0route 开始========================================================//
    Route::get("/video/player.html", "VideoController@player");
    //Route::any("/json/video/player/{id}.json", "VideoController@playerJson");

    Route::get('/video', 'VideoController@videosDefault');//最新 视频终端列表
    Route::get('/video_{page}.html', 'VideoController@videosDefault');//最新视频 分页

    Route::get("/{name_en}/video_{page}.html", "VideoController@videosByNameEn");//专题 视频列表 分页
    Route::get('{name_en}/video{id}.html', 'VideoController@videoDetailByNameEn');//专题 视频终端
    Route::get('/video{id}.html', 'VideoController@videoDetail');//视频终端


    Route::get('/video/basketballstar_{id}_{page}.html', 'VideoController@videosByBasketballStar');//篮球球星 视频列表 分页
    Route::get('/video/basketballstar_{id}', 'VideoController@videosByBasketballStar');//篮球球星 视频列表


    Route::get('/video/footballstar_{id}_{page}.html', 'VideoController@videosByFootballStar');//足球球星 视频列表 分页
    Route::get('/video/{type}_{page}.html', 'VideoController@videos');//视频列表 （足球、篮球、球星、其他）
    Route::get('/video/footballstar_{id}', 'VideoController@videosByFootballStar');//足球球星 视频列表


    Route::get("/{name_en}/video", "VideoController@videosByNameEn");//专题 视频列表


    Route::get('/video/{type}', 'VideoController@videos');//视频列表 （足球、篮球、球星、其他）分页

    //----------------------------------------------------------------------------------------------------------------------------------

    //静态化开始
    Route::get("/static/video/player", "VideoController@staticVideoPlayer");//静态化 视频player
    Route::get("/static/video/list/{tab}/{page?}", "VideoController@staticVideosTabHtml");//静态化 视频 tab（最新、篮球、足球等） 列表页面
    Route::get("/static/video/list-leg/{name_en}/{page?}", "VideoController@staticVideosLeagueHtml");//静态化 视频 专题列表页
    Route::get("/static/video/list-tag/{tagId}-{sport}/{page?}", "VideoController@staticVideosTagHtml");//静态化 视频 球星列表页

    Route::get("/static/video/detail/{id}", "VideoController@staticVideoDetail");//静态化录像终端
    Route::get("/static/video/detail-all/{page?}", "VideoController@staticAllVideoDetail");//评论静态化视频终端页
    //静态化列表页、分页

    //========================================================视频2.0route 结束========================================================//

    //========================================================专题页面========================================================//
    Route::get('/v2/{name_en}', 'SubjectController@detailV2');//专题终端页 英超、中超、等等
    Route::get('/{name_en}', 'SubjectController@detail');//专题终端页 英超、中超、等等

    //========================================================专题页面========================================================//
});

//录像 2.0版本
Route::group(["namespace" => 'Record'], function () {
    Route::get('/record/other{id}.html', 'RecordController@detail2');//终端
    Route::get('/{name_en}/record{id}.html', 'RecordController@detail');//终端
    Route::get('/record/index.html', 'RecordController@index');//首页
    Route::get('/{name_en}/record/index{pageNo}.html', 'RecordController@subject');//专题录像
    Route::get('/{name_en}/record/index.html', 'RecordController@subject');//专题录像

    Route::get("/static/record/{id}", "RecordController@recordDetailHtml");//静态化终端
    Route::get("/static/record_index", "RecordController@staticIndex");//静态化首页
    Route::get("/static/record_subject/{league}/{page}", "RecordController@subjectDetailHtml");//静态化专题列表
});