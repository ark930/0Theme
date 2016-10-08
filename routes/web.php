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

Route::group(['middleware' => []], function() {
    Route::get('/plan', 'MainController@showPlan');
    Route::post('/plan', 'MainController@setPlan');
    Route::get('/plan/info', 'MainController@showPlanInfo');

    Route::get('/payment/experience/create', 'PaymentController@createExperience');
    Route::get('/payment/create', 'PaymentController@create');
    Route::get('/payment/confirm', 'PaymentController@confirm');
    Route::post('/payment/refund', 'PaymentController@refund');
    Route::get('/payment/sale', 'PaymentController@getSale');

    Route::post('/theme/download', 'ThemeController@download');
    Route::post('/theme/update/check', 'ThemeController@checkUpdate');
    Route::post('/theme/update', 'ThemeController@update');

});