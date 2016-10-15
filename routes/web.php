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

// Authentication Routes...
Route::get('login', 'Auth\LoginController@showLoginForm')->name('login');
Route::post('login', 'Auth\LoginController@login');
Route::post('logout', 'Auth\LoginController@logout');

// Registration Routes...
Route::get('register', 'Auth\RegisterController@showRegistrationForm');
Route::post('register', 'Auth\RegisterController@register');


// Password Reset Routes...
Route::get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm');
Route::post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
Route::get('password/email', function () { return view('dashboard.sendemailsuccess'); })->name('password_email_sent');
Route::get('password/reset/{email}/{token}', 'Auth\ResetPasswordController@showResetForm')->name('password_reset_token');
Route::post('password/reset', 'Auth\ResetPasswordController@reset');

Route::get('/themes', 'MainController@theme');

// The following routes can be used when user login
Route::group(['middleware' => ['auth']], function() {
    Route::get('register/confirm', 'Auth\RegisterController@registerConfirmPage')->name('register_confirm');
    Route::get('register/confirm/{confirm_code}', 'Auth\RegisterController@registerConfirmWithCode')
        ->where('confirm_code', '[0-9a-zA-Z]+')
        ->name('register_confirm_with_code');

    // The following routes can be used when user confirmed the register email
    Route::group(['middleware' => ['register_confirm_check']], function() {
        // Plan Routes
        Route::get('/plan', 'PlanController@showPlan');
        Route::get('/plan/{membership}', 'PlanController@showPlanDetails')->where('membership', 'basic|pro|lifetime');

        // Payment Routes
        Route::post('/payment/create', 'PaymentController@create');
        Route::get('/payment/success', 'PaymentController@paySuccess');
        Route::get('/payment/fail', 'PaymentController@payFail');
        Route::get('/payment/sale', 'PaymentController@getSale');

        // Dashboard Routes
        Route::get('/dashboard', 'MainController@dashboard');

        // The following routes can be used by who is a non-free user
        Route::group(['middleware' => ['user_check']], function() {
            // Theme Routes
            Route::post('/theme/download', 'ThemeController@download');
            Route::post('/theme/update/check', 'ThemeController@checkUpdate');
            Route::post('/theme/update', 'ThemeController@update');
        });
    });
});

// for testing
Route::get('/payment/experience/create', 'PaymentController@createExperience');
Route::post('/payment/refund', 'PaymentController@refund');
