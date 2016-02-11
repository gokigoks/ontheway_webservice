<?php
/**
 * Created by PhpStorm.
 * User: eldringoks
 * Date: 2016-02-11
 * Time: 10:02 PM
 */

namespace App\Classes;

use App\Classes\tokenGenerator;
use App\User;
use Cache;
use Session;
use Input;
use Auth;
use Response;

class UserSessionHandler
{
    /**
     * @param $credentials
     * @return \Illuminate\Http\JsonResponse
     */
    public static function login($credentials)
    {
        if (Auth::attempt($credentials)) {
            /*
             * $token web token for user session
             */
            $token = new tokenGenerator;
            $user_object = Auth::user();
            $session = collect([]);
            $session->offsetSet('user', $user_object);
            $user_object->setAttribute('token', $token->uuid);
            self::startUserSession($session, $token->uuid);

            return $user_object;
        } else {
            return Response::json("error.. bad credentials", 400);
        }
    }

    public static function logout($token)
    {

    }

    public static function cacheRequest()
    {

    }

    /**
     * @param $session
     * @param $token
     */
    public static function startUserSession($session, $token)
    {
        //start a user session
        Session::put($token.'.session', $session);
    }

    public static function addIterinarySession($token, $iterinary)
    {
        Session::put($token.'.iterinary',$iterinary);
    }

    public static function addSegmentSession($token, $segment)
    {
        Session::put($token.'.segment',$segment);
    }

    public static function getCurrentIterinary($token)
    {

    }


}