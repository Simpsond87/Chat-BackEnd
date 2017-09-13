<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::post('signIn', 'UserController@signIn');
Route::post('signUp', 'UserController@signUp');
Route::get('getUser', 'UserController@getUser');
Route::get('getUsers', 'UserController@getUsers');

Route::get('getMessages/{id}', 'MessagesController@getMessages');
Route::post('sendMessage', 'MessagesController@sendMessage');

Route::any('{path?}', 'MainController@index')->where("path", ".+");
