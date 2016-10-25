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

# Authentication Routes
Route::get('login','SessionController@getLogin');
Route::post('login','SessionController@postLogin')->name('post.login');
Route::get('logout', 'SessionController@logout');

# Registration Routes
Route::get('register','RegistrationController@getRegister');
Route::post('registration','RegistrationController@postRegister')->name('registration.post');
Route::get('activate/{id}/{code}', 'RegistrationController@activate');
