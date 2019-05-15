<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index')->name('admin.home');
    $router->resource('/goods', GoodsController::class);
    $router->resource('/user', UserController::class);
    $router->resource('/material', MaterialController::class);
    $router->post('/material/addImg', 'MaterialController@addImg')->name('admin.material');
    $router->resource('/list', ListController::class);
    $router->resource('/lists', ListsController::class);
    $router->resource('/news', NewsController::class);
    //$router->get('/news/sendMessage', 'NewsController@sendMessage')->name('admin.news');
    Route::get('/sendMessage','NewsController@sendMessage');
    $router->resource('/menu', MenuController::class);
    $router->post('/menu/addMenu', 'MenuController@addMenu')->name('admin.menu');
    $router->resource('/menulist', MenulistController::class);
    Route::get('createMenu','MenulistController@createMenu');
    Route::get('/test','NewsController@test');
    Route::get('/test2','NewsController@test2');
    $router->resource('/channel', ChannelController::class);
    $router->resource('/channellist', ChannellistController::class);
    $router->post('/channel/addChannel', 'ChannelController@addChannel')->name('admin.channel');
});
//Route::get('/material','Admin\MaterialController@grid');
//Route::any('/material/addImg','Admin\MaterialController@addImg');
