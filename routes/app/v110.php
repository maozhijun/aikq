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
    Route::get("/config.json", "HomeController@appConfig");
    Route::get("/lives/{sport}/{mid}.json", 'Live\LiveController@appLiveDetail');
});