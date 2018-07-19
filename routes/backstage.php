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

    Route::get("/matches", "BsController@matches");//主播预约页面
    Route::any("/matches/find", "BsController@findMatches");//查找比赛
    Route::any("/matches/team/color", "BsController@setTeamColor");//设置球队球衣颜色
    Route::any("/matches/score/set", "BsController@setShowScore");//设置预约赛事是否显示对阵信息
    Route::any("/matches/book", "BsController@bookMatch");//预约比赛
    Route::any("/matches/book/cancel", "BsController@cancelBookMatch");//取消预约
});
