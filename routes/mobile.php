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
    Route::get('/',"LiveController@lives");
});

/**
 * 直播入口
 */
Route::group(["namespace" => 'Live'], function () {
    Route::any("/", function (){
        return redirect('/m/lives.html');
    });
    Route::get("/lives.html", "LiveController@lives");//直播列表
    Route::get("/football.html", "LiveController@footballLives");//直播列表
    Route::get("/basketball.html", "LiveController@basketballLives");//直播列表

    Route::get("/live/football/{id}.html", "LiveController@footballdetail");//直播终端
    Route::get("/live/basketball/{id}.html", "LiveController@basketballDetail");//直播终端

//    Route::get('/lives/player.html', function () {return view('mobile.live.player');});
//    Route::get("/lives/roll/{time}/{id}.html", "LiveController@roll");//直播滚球
//    Route::get("/lives/data/{time}/{id}.html", "LiveController@match_data");//直播数据
//    Route::get("/lives/tip/{time}/{id}.html", "LiveController@matchTip");//直播提点数据
    Route::get("/lives/data/refresh.json", "LiveController@match_live");//比赛比分数据

//    Route::get("/live/football/recommend/{mid}", "LiveController@getArticleOfFMid");//直播终端足球推荐
//    Route::get("/live/basketball/recommend/{mid}", "LiveController@getArticleOfBMid");//直播终端篮球推荐

//    //天天直播
//    Route::get("/match/live/url/zb/{id}", 'LiveController@getTTZBLiveUrl');
//    //无插件
//    Route::get("/match/live/url/wcj/{id}", 'LiveController@getWCJLiveUrl');
//    //channel通用
//    Route::get("/match/live/url/channel/{id}.json", 'LiveController@getLiveUrl');
});