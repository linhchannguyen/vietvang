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

Route::group(['prefix' => '/admin'], function () {
    Route::get('/login', 'UserController@login')->name('login');
    Route::post('/login', 'UserController@do_login')->name('login');
    Route::get('/signup', 'UserController@signup')->name('signup');
    Route::post('/signup', 'UserController@store')->name('signup');
    Route::get('/forget-password', 'UserController@forget_password')->name('forget-password');
    Route::post('/sendMail','UserController@sendMail')->name('send-mail');
    Route::get('/reset-pass/{token}/{email}','UserController@reset_link')->name('reset-link');
    Route::post('/do-reset','UserController@do_reset')->name('do-reset');
    Route::get('/404_page', function(){
        return view('admin.404_page');
    });
});
// Route group
// Route::group(['prefix' => '/admin'], function () {
Route::group(['prefix' => '/admin', 'middleware' => array('admin')], function () {
    Route::get('/', 'UserController@index')->name('/');
    Route::get('/user-list', 'UserController@user_list')->name('user-list');
    Route::get('/logout', 'UserController@logout')->name('logout');
});