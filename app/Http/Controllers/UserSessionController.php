<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use App\User;
use Cache;
use Input;
use App\Classes\UserSessionHandler;
use Illuminate\Http\Request;

class UserSessionController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function login()
	{
        $credentials = array(
            'email' => Input::get('email'),
            'password' => Input::get('password'),
        );

        $response = UserSessionHandler::login($credentials);
        return response()->json($response['body'],$response['http_code']);

	}


	public function register(Request $request)
	{

	}

	/**
	 * clear session. deletes user assigned web token
	 * @param $request
	 * @return Response
	 */
	public function logout(Request $request)
	{
        $token = $request['token'];
//        dd(UserSessionHandler::getByToken($token), \Session::all());
        if (UserSessionHandler::check($token)) {
            Auth::logout();
            UserSessionHandler::logout($token);

            return response()->json('user logged out..', 200);
        } else {
            return response()->json('user wasnt even logged in, dummy!', 401);
        }
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
