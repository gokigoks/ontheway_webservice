<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'WelcomeController@index');

Route::get('home', 'HomeController@index');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);

Route::get('test/foursquare','ApiController@get_foursquare');
Route::get('test/rome2rio','ApiController@get_rome2rio');

Route::post('test/foursquare','ApiController@post_foursquare');
Route::post('test/rome2rio','ApiController@post_rome2rio');

Route::get('test/api/dump', ['middleware' => 'cors', 'uses' => 'ApiController@testAjax']);