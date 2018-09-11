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

/**
 * 直播入口
 */
Route::group(["namespace" => 'Live'], function () {
    Route::any("/", function (){
        return redirect('/mip/lives.html');
    });
    Route::any("/football.html", function (){
        return redirect('/mip/lives.html');
    });
    Route::any("/basketball.html", function (){
        return redirect('/mip/lives.html');
    });
    Route::any("/other.html", function (){
        return redirect('/mip/lives.html');
    });
    Route::get("/lives.html", "LiveController@lives");//直播列表

    Route::get("/live/football/{id}.html", "LiveController@footballdetail");//直播终端
    Route::get("/live/basketball/{id}.html", "LiveController@basketballDetail");//直播终端
    Route::get("/live/other/{id}.html", "LiveController@otherDetail");//直播终端

    Route::get("/live/subject/videos/{type}/{page}.html", 'LiveController@subjectVideos');//录像列表
    Route::get("/live/subject/video/{first}/{second}/{vid}.html", 'LiveController@subjectVideoDetail');//录像终端
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
    Route::any("/news/", function (){
        return redirect('/mip/news/index.html');
    });
    Route::get("/news/index.html", "ArticleController@articles");//文章列表
    Route::get("/news/{type}/{date}/{id}.html", "ArticleController@detail");//文章终端
    Route::get("/news/{type}", "ArticleController@articlesCell");//文章列表翻页
});

/**
 * 赛事专题
 */
Route::group(["namespace" => 'Subject'], function () {
    foreach (\App\Http\Controllers\Controller::SUBJECT_NAME_IDS as $name=>$item) {
        Route::get("/$name/", "SubjectController@detail");//专题页
    }
});

Route::get("/test/{id}", "Subject\\SubjectController@staticSubjectDetailJson");
