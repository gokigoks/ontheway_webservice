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
use App\Iterinary;
use App\Segment;
use App\Activity;

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
        //if(Auth::check()) return ['body'=>'logged in naman ka','http_code' => 200];
        if (Auth::attempt($credentials)) {
            /*
             * $token web token for user session
             */
            //if()
            $token = new tokenGenerator;
            $user = Auth::user();

            $planned_iterinaries = $user->planned_iterinaries()->with('route.segments')->get();
            $past_iterinaries = $user->past_iterinaries()->with('route.segments')->get();

            $current_iterinary = self::getCurrentIterinary($user);
            $session = collect(['user'=>$user]);
            $session->offsetSet('plannedIterinaries', $planned_iterinaries);
            $session->offsetSet('pastIterinaries', $past_iterinaries);
            $session->offsetSet('currentIterinary', $current_iterinary);
            //dd($session);
            $user->setAttribute('token' , $token->uuid);
            self::startUserSession($user->id, $token->uuid);

            return ['body'=>$session,'http_code' => 200];
        } else {

            return ['body'=>'error bad credentials','http_code' => 400];
        }
    }

    /**
     * @param $token
     * @return bool
     */
    public static function check($token)
    {
        if(Session::has($token)){
            return true;
        }
        return false;
    }

    /**
     * @param $token
     * @return mixed
     */
    public static function getByToken($token)
    {
        return Session::get($token);
    }

    /**
     * @param $token
     * @return mixed
     */
    public static function user($token)
    {
        $user_id = self::getByToken($token);
        return User::find($user_id);
    }

    /**
     * @param $user
     * @return $iterinary
     */
    public static function getCurrentIterinary($user)
    {
        $iterinary = $user->current_iterinary()->with('route.segments')->get();
        return $iterinary;
    }

    public static function logout($token)
    {
        Session::forget($token);
    }

    /**
     * @param id
     * @param $token
     */
    public static function startUserSession($id, $token)
    {
        //start a user session
        Session::put($token, $id);
    }

    public static function addIterinarySession($token, $iterinary)
    {
        Session::put($token.'.iterinary',$iterinary);
    }

    public static function addSegmentSession($token, $segment)
    {
        Session::put($token.'.segment',$segment);
    }



}