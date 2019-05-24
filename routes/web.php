<?php
/**
 * Create by: Nguyen Linh Chan
 * Date: 13/5/2019
 * Place: Viet Vang Company
 */
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
Route::get('/welcome', function(){
    return view('welcome');
});

Route::get('/login', 'UserController@login')->name('login');
Route::post('/login', 'UserController@do_login')->name('login');

Route::group(['prefix' => '/user'], function () {
    Route::get('/signup', 'UserController@user_signup')->name('signup');
    Route::post('/signup', 'UserController@user_store')->name('user_signup');
});

Route::group(['prefix' => '/admin'], function () {
    Route::get('/signup', 'UserController@admin_signup')->name('signup');
    Route::post('/signup', 'UserController@admin_store')->name('admin_signup');
    Route::get('/forget-password', 'UserController@forget_password')->name('forget-password');
    Route::post('/sendMail','UserController@sendMail')->name('send-mail');
    Route::get('/reset-pass/{token}/{email}','UserController@reset_link')->name('reset-link');
    Route::post('/do-reset','UserController@do_reset')->name('do-reset');
    Route::get('/404_page', function(){
        return view('admin.404_page');
    });
});

// Route group
Route::group(['prefix' => '/admin', 'middleware' => array('admin')], function () {
    Route::get('/', 'UserController@index')->name('/');
    Route::get('/user-list', 'UserController@user_list')->name('user-list');    
    Route::delete('/delete_user/{id}', 'UserController@delete_one');
    Route::post('/delete_ajax', 'UserController@delete_user');
    Route::put('/update_user/{id}', 'UserController@update_user');
    Route::get('/logout', 'UserController@logout')->name('logout');
    Route::post('/import', 'UserController@import')->name('import');
    Route::get('/export', 'UserController@export')->name('export');
});