<?php

/*
|--------------------------------------------------------------------------
| backstage Routes
|--------------------------------------------------------------------------
|
*/
Route::group([], function () {
    Route::any("/login", "AuthController@login");
    Route::any("/logout", "AuthController@logout");

    Route::any("/password/edit", "AuthController@passwordEdit");
    Route::get("/info", "AuthController@info");
});
