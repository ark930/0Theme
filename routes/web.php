<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
Route::get('register/confirm', 'Auth\RegisterController@emailConfirmPage')->name('register_confirm');
Route::get('register/confirm/{confirm_code}', 'Auth\RegisterController@emailConfirmWithCode')
    ->where('confirm_code', '[0-9a-zA-Z]+')
    ->name('register_confirm_with_code');

Route::get('/home', 'HomeController@index');

Route::group(['middleware' => 'auth'], function() {
//    Route::get('/overview', 'MainController@overview');
//    Route::get('/designer', 'MainController@designer');
//    Route::get('/artist', 'MainController@artist');
//    Route::get('/photographer', 'MainController@photographer');
//    Route::get('/blogger', 'MainController@blogger');
//    Route::get('/startup', 'MainController@startup');
});