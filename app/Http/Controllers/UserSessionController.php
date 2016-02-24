<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Auth;
use App\User;
use Cache;
use Input;
use App\Classes\tokenGenerator;
use Validator;
use App\Classes\UserSessionHandler;
use Illuminate\Http\Request;
use Illuminate\Foundation\Validation\ValidatesRequests;


class UserSessionController extends Controller
{

    use ValidatesRequests;

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
        return response()->json($response['body'], $response['http_code']);

    }



    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function register(Request $request)
    {
        $request = $request->all();

        $validator = Validator::make($request, [
            'email' => 'required|email|unique:users|min:6',
            'name' => 'required|min:3',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) return response()->json($validator->messages(), 422);

        $user = new User;
        $user->email = $request['email'];
        $user->name = $request['name'];
        $user->password = bcrypt($request['password']);
        $user->save();

        $token = new tokenGenerator;

        $user->setAttribute('token', $token->uuid); // add token to returned object

        UserSessionHandler::startUserSession($user->id,$token->uuid); // starts session

        dd(\Session::get($token->uuid));
        return response()->json($user, 200);
    }

    /**
     * clear session. deletes user assigned web token
     * @param $request
     * @return Response
     */
    public function logout(Request $request)
    {
        $token = $request['token'];

        //if(Auth::check()) return response()->json('nka login lage ka?', 200);
        if (!$token) {
            return response()->json('empty token', 400);
        }
        //dd(UserSessionHandler::getByToken($token), \Session::all());
        if (UserSessionHandler::check($token)) {

            Auth::logout();
            UserSessionHandler::logout($token);

            return response()->json('user logged out..', 200);
        } else {

            return response()->json('invalid token to be logged out!', 401);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

    public function rateIterinary(Request $request)
    {


        return response()->json('success',200);
    }

}
