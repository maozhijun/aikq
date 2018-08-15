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

//主播
Route::group(["namespace" => 'Anchor'], function () {
    Route::get("/anchor/index.json", "AnchorController@appV110");//首页
});

Route::group([], function () {
//app配置
    Route::get("/config.json", "HomeController@appConfigV110");
    //比赛 录像
    Route::get("/lives/{sport}/{mid}.json", 'Live\LiveController@appLiveDetail');

    //主播
    Route::get('/anchor/room/url/{room_id}.json','Anchor\AnchorController@playerUrlApp');

    //直播中
    Route::get('/anchor/living.json','Anchor\AnchorController@livingRoom');

    //文章相关
    Route::get('/news/list/{type_en}/{page}','Article\ArticleController@appNewsList'); //文章列表
    Route::get('/news/types','Article\ArticleController@appNewsTypes');//文章类型
    Route::get('/news/detail/{type_en}/{id}','Article\ArticleController@appNewsDetail');//文章终端
});