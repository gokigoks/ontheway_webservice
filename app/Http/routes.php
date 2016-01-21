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

Route::get('test/api/users',['middleware' => 'cors', 'ApiConrtoller@get_users']);



Route::post('login', ['middleware' => 'cors', 'uses' => function()
{       
        
    $credentials = array(
        'email' => Input::get('email'), 
        'password' => Input::get('password'),            
    );


    if (Auth::attempt( $credentials ))
    {   
        $user_object = json_encode(Auth::user());
        return Response::json($user_object,200);
        //return Redirect::to_action('user@index'); you'd use this if it's not AJAX request
    }else{
        return Response::json("error.. bad credentials", 400);
        /*return Redirect::to_action('home@login')
        -> with_input('only', array('new_username')) 
        -> with('login_errors', true);*/
    }
}]);
// Route::group(['middleware' => 'cors'], function(){


//     // Authentication routes...
//     // Route::post('auth/login', function(){ 
    
//     //     return response()->json('hello',200);
    
//     // });
//     //Route::get('auth/login', 'Auth\AuthController@getLogin');
//     Route::post('auth/login', 'Auth\AuthController@postLogin');
//     Route::get('auth/logout', 'Auth\AuthController@getLogout');

//     // Registration routes...
//     Route::get('auth/register', 'Auth\AuthController@getRegister');
//     Route::post('auth/register', 'Auth\AuthController@postRegister');
//     // password route
//     Route::get('user/login','SessionController@getLogin');
//     Route::post('user/login','SessionController@doLogin');


// });

/*
*   API SERVICE ROUTES
*
**/

Route::get('get/hotels', 'ApiController@get_hotels');

Route::get('pingServer',['middleware' => 'cors', function(){
    /*
    *   This is a test route
    *
    **/
    return response()->json('server up',200);

}]);