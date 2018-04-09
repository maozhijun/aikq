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
    Route::get('/spread/matchList.html', 'ShareController@index');
    Route::get('/spread/matchListUrl.html', 'ShareController@url');
    //Route::get('/',"HomeController@index");
});
