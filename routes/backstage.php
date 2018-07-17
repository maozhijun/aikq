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

    Route::get("/matches", "BsController@matches");
});
