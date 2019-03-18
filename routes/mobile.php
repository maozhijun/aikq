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

Route::group(["namespace" => 'Live'], function () {
    //Route::get('/',"LiveController@lives");
});

//世界杯专题
Route::group(["namespace" => 'FIFA'], function () {
    Route::get("/worldcup/2018/index.html", "WorldCupController@index");
    Route::get("/worldcup/2018/rank.html", "WorldCupController@rank");
    Route::get("/worldcup/2018/team_index.html", "WorldCupController@teamIndex");
    Route::get("/worldcup/2018/team/{tid}.html", "WorldCupController@teamDetail");
    Route::get("/worldcup/2018/topic/index.html", "WorldCupController@topicList");

    //静态化,只有球队列表才在这里做一次,没有必要定时任务,一次就够了
    Route::get("/static/worldcup/team_index","WorldCupController@staticTeamIndex");
    Route::get("/static/worldcup/rank","WorldCupController@staticRank");
    Route::get("/static/worldcup/index","WorldCupController@staticIndex");
    Route::get("/static/worldcup/team/{tid}","WorldCupController@staticTeamDetail");
    Route::get("/static/worldcup/topic","WorldCupController@staticTopicList");
});

/**
 * 直播入口
 */
Route::group(["namespace" => 'Live'], function () {
    Route::any("/", "LiveController@lives");
    Route::get("/lives", "LiveController@lives");//直播列表
    Route::get("/lives/data/refresh.json", "LiveController@match_live");//比赛比分数据
});

/**
 * 视频相关
 */
Route::group(["namespace"=>"Video"], function () {
    Route::get("/video/footballstar_{id}", 'VideoController@videosByFootballStar');//视频列表 足球球星
    Route::get("/video/basketballstar_{id}", 'VideoController@videosByBasketballStar');//视频列表 篮球球星
    Route::get("/video/{type?}", 'VideoController@videos');//视频列表 类型

    Route::get("/video{id}.html", "VideoController@videoDetail");//视频终端
    Route::get("{name_en}/video{id}.html", "VideoController@videoDetailByNameEn");
});

//主播
Route::group(["namespace" => 'Anchor'], function () {
    Route::get("/anchor/index.html", "AnchorController@index");//比赛比分数据
    Route::get("/anchor/room/{room_id}.html", "AnchorController@room");//比赛比分数据
});

/**
 * 文章列表、终端
 */
Route::group(["namespace" => 'Article'], function () {
    Route::get("/news/index.html", "ArticleController@articles");//文章列表
    Route::get("/news/", "ArticleController@articles");//文章列表
    Route::get("/news/page{page}.html", "ArticleController@articlesPage");//文章列表分页

    Route::get("/news/{param}.html", "ArticleController@detail");//文章终端页
    Route::get("/{name_en}/news{id}.html", "ArticleController@detailByName");//文章终端页 与上面的一样，只是匹配路径不一样

    Route::get("/{name_en}/news/page{page}.html", "ArticleController@subjectNews");//专题页文章终端翻页
});

/**
 * 赛事专题
 */
Route::group([], function () {
    Route::get("/v2/{name_en}/{season?}", "Subject\\SubjectController@detailV2");//专题页
    foreach (\App\Http\Controllers\Controller::SUBJECT_NAME_IDS as $name=>$id) {
        Route::get("/$name/", "Subject\\SubjectController@detail");//专题页
        Route::get("/$name/video{id}.html", "Live\\LiveController@subjectVideoDetail");//专题录像终端
    }
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
});

Route::group(["namespace" => 'Data'], function () {
    Route::get("/data/", "DataController@index");
    //静态化
    Route::get("/static/data_index/", "DataController@staticIndex");
});