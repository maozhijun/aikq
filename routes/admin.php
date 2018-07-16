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

Route::group(['middleware' => 'admin_auth'], function () {
    Route::any("/", function () {
        return view('admin.index');
    });
    Route::any("/index", function () {
        return view('admin.index');
    });
});

/**
 * 用户相关
 */
Route::group(['namespace'=>'Role'], function () {
    Route::any("/login", "AuthController@index");
    Route::any("/logout", "AuthController@logout");

    Route::any("/accounts", "AuthController@accounts");//用户列表
    Route::any("/account/new", "AuthController@put");//新建用户
    Route::any("/account/update", "AuthController@put");//修改用户
    Route::any("/account/delete", "AuthController@delete");//删除用户
});

/**
 * 权限相关
 */
Route::group(['namespace'=>'Role', 'middleware' => 'admin_auth'], function () {
    Route::any("/roles", "RoleController@index");//角色列表
    Route::any("/roles/detail", "RoleController@detail");//新建、修改 角色页面
    Route::post("/roles/save", "RoleController@saveRole");//保存角色资料
    Route::post("/roles/del", "RoleController@delRole");//删除角色资料

    Route::any("/resources", "ResourceController@resources");//权限列表
    Route::post("/resources/save", 'ResourceController@saveRes');//保存权限
    Route::post("/resources/del", 'ResourceController@delRes');//删除权限
});

/**
 * 直播相关
 */
Route::group(['namespace'=>'Match', 'middleware' => 'admin_auth'], function () {
    Route::any('/live/matches', 'MatchController@todayMatch');//今天的足球赛事列表
    Route::any('/live/matches/basketball', 'MatchController@todayBasketMatch');//今天的篮球赛事列表

    Route::post('/live/matches/save', 'MatchController@saveMatchLive');//设置赛事直播链接
    Route::post('/live/matches/channel/save', 'MatchController@saveChannel');//设置赛事直播频道
    Route::post('/live/matches/channel/del', 'MatchController@delChannel');//删除赛事直播频道

    Route::post('/live/matches/live/save-impt', 'MatchController@changeLiveImp');//设置重点赛事
    Route::post('/live/matches/channel/random-code', 'MatchController@randomCode');//生成高清验证码

    Route::any('/live/error', 'MatchController@liveErrorList');//直播错误列表
    Route::any('/live/error/delete','MatchController@liveErrorDel');//清空错误直播列表

    //Route::any('/cache-manager', 'CacheManagerController@index');//缓存刷新
    Route::get('/other/matches', 'OtherMatchController@matches');//自建赛事列表
    Route::post('/other/matches/save', 'OtherMatchController@saveOther');//自建赛事保存
});



/**
 * 专题相关
 */
Route::group(['namespace'=>'Subject', 'middleware' => 'admin_auth'], function () {

    //专题 操作 开始
    Route::get('/subject/leagues', 'SubjectController@subjectLeagues');//专题列表
    Route::get('/subject/leagues/edit', 'SubjectController@edit');//新建/修改 专题页面

    Route::post('/subject/leagues/find-league', 'SubjectController@findLeague');//根据名称获取赛事
    Route::post('/subject/leagues/save', 'SubjectController@saveLeague');//保存/修改 专题
    Route::post('/subject/leagues/change', 'SubjectController@changeSL');//修改专题隐藏/显示状态
    //专题 操作 结束

    //资讯操作 开始
    Route::get('/subject/articles', 'SubjectArticleController@articles');//专题资讯列表
    Route::post('/subject/articles/save', 'SubjectArticleController@saveArticle');//保存专题资讯
    Route::post('/subject/articles/del', 'SubjectArticleController@deleteArticle');//删除专题资讯
    //资讯操作 结束

    //集锦操作 开始
    Route::get('/subject/specimens', 'SubjectSpecimenController@specimens');//专题集锦列表
    Route::get('/subject/specimens/edit', 'SubjectSpecimenController@edit');//专题集锦 新增/修改 页面

    Route::post('/subject/specimens/save', 'SubjectSpecimenController@saveSpecimen');//保存 专题集锦
    Route::post('/subject/specimens/del', 'SubjectSpecimenController@deleteSpecimens');//删除 专题集锦
    //集锦操作 结束

    //录像操作 开始
    Route::get('/subject/videos', 'SubjectVideoController@videos');//专题录像列表

    Route::get('/subject/videos/find-matches', 'SubjectVideoController@findMatches');//查找比赛
    Route::post('/subject/videos/save', 'SubjectVideoController@saveVideo');//保存专题录像
    Route::get('/subject/videos/del', 'SubjectVideoController@delVideo');//删除专题录像

    Route::post('/subject/videos/save-channel', 'SubjectVideoController@saveVideoChannel');//保存录像线路
    Route::get('/subject/videos/del-channel', 'SubjectVideoController@delVideoChannel');//删除录像线路
    //录像操作 结束
});

Route::group(['middleware' => 'admin_auth'], function () {
    Route::post("/upload/cover/", "UploadController@uploadCover");//上传封面
});