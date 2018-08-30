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

    Route::get("/live/subject/videos/{type}/{page}.html", 'LiveController@subjectVideos');//录像列表
    Route::get("/live/subject/video/{first}/{second}/{vid}.html", 'LiveController@subjectVideoDetail');//录像终端
    Route::get("/lives/data/refresh.json", "LiveController@match_live");//比赛比分数据
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
});

/**
 * 赛事专题
 */
Route::group(["namespace" => 'Subject'], function () {
    foreach (\App\Http\Controllers\Controller::SUBJECT_NAME_IDS as $name=>$id) {
        Route::get("/$name/", "SubjectController@detail");//专题页
    }
});

/**
 * 直播终端
 */
Route::group(["namespace" => 'Live'], function () {
    Route::get('/live{param}.html',"LiveController@detail");//足球直播页、篮球直播页、自建赛事直播页
    Route::get('/{name_en}/live{param}.html',"LiveController@detailBySL");//足球直播页、篮球直播页、自建赛事直播页
});
