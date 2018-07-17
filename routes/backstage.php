<?php

/*
|--------------------------------------------------------------------------
| backstage Routes
|--------------------------------------------------------------------------
|
*/
Route::group([], function () {
    Route::any("/login", "BsController@login");
    Route::any("/logout", "BsController@logout");

    Route::any("/password/edit", "BsController@passwordEdit");
    Route::get("/info", "BsController@info");
    Route::post("/info/save", "BsController@saveInfo");

    Route::post("/info/room/start", "BsController@startLive");//开播，获取推流地址
    Route::post("/info/room/end", "BsController@endLive");//结束直播

    Route::get("/matches", "BsController@matches");
    Route::any("/matches/find", "BsController@findMatches");
    Route::any("/matches/book", "BsController@bookMatch");
    Route::any("matches/book/cancel", "BsController@cancelBookMatch");
});
