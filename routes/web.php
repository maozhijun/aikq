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


