<?php
/**
 * Created by PhpStorm.
 * User: eldringoks
 * Date: 2016-02-11
 * Time: 10:02 PM
 */

namespace App\Classes;

use App\Classes\tokenGenerator;
use App\Classes\GeolocationHelper;
use App\User;
use App\Iterinary;
use App\Route;
use App\Segment;
use App\Activity;
use Carbon\Carbon;
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
            $session = collect(['user' => $user]);
            $session->offsetSet('plannedIterinaries', $planned_iterinaries);
            $session->offsetSet('pastIterinaries', $past_iterinaries);
            $session->offsetSet('currentIterinary', $current_iterinary);
            //dd($session);
            $user->setAttribute('token', $token->uuid);
            self::startUserSession($user->id, $token->uuid);

            return ['body' => $session, 'http_code' => 200];
        } else {

            return ['body' => 'error bad credentials', 'http_code' => 400];
        }
    }

    /**
     * @param Request $request
     */
    public static function register(Request $request)
    {
        /**
         * TODO
         * simple registration
         */
    }

    /**
     * @param $token
     * @return bool
     */
    public static function check($token)
    {
        if (Session::has($token)) {
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
        Session::regenerate();
    }

    public static function addIterinarySession($token, $iterinary)
    {
        Session::put($token . '.iterinary', $iterinary);
    }

    public static function addSegmentSession($token, $segment)
    {
        Session::put($token . '.segment', $segment);
    }


    /**
     * @param $token
     * @param $iterinary_id
     * @param $origin_name
     * @param $lng
     * @param $lat
     * @param $mode
     * @return \Illuminate\Http\JsonResponse
     */
    public static function addSegment($token, $iterinary_id, $origin_name, $lng, $lat, $mode)
    {
        $segment = new Segment;
        $segment->origin_name = $origin_name;
        $segment->origin_pos = $lat . ',' . $lng;
        $segment->mode = $mode;
        $iterinary = Iterinary::findOrFail($iterinary_id);
        $route = $iterinary->route()->first();
//        dd($route->segments()->count());
        $segment->sequence = $route->segments()->count() + 1;

        //dd($route->count());
        $user = self::user($token);
        $segment->day = self::getDiffInDays($user, $iterinary_id);

        //dd($route);
        if ($route->count() < 1) {
            $route = new Route;
            $route->name = $iterinary->origin . ' to ' . $iterinary->desination;
            try {
                $route->save();
                $iterinary->route()->associate($route);
                $iterinary->save();
            } catch (\Exception $e) {
                //dd($iterinary,$route);
                return response()->json('error saving route', 400);
            }
        }

        $route = $iterinary->route()->first();
        $route->segments()->save($segment);


        //dd($segment->getAttributes());
        self::newUserActivity('transport', $segment->getAttribute('id'), $token);


        return response()->json('success', 200);
    }


    public static function addSpot($token, $spot_name, $category, $tips, $lat, $lng, $segment_id)
    { //todo
        $segment = Segment::find($segment_id);
        $request_type = 'spot';
        $activity = new Activity();
        $activity->save();
        self::newUserActivity('spot', $activity->id, $token);

    }


    public static function addFood($token, $place_name, $lng, $lat, $tips, $category, $segment_id)
    {
        //TODO
        $request_type = 'eat';
        $activity = new Activity();
        self::newUserActivity('eat', $activity->id, $token);
    }

    /**
     * @param $user
     * @param $iterinary_id
     * @return mixed
     */
    public static function getDiffInDays($user, $iterinary_id)
    {
        $now = Carbon::now();
        $start_date = Carbon::now()->subDays(2);
        $pivot = $user->iterinaries()->find($iterinary_id);

        $date = new Carbon($pivot->pivot->date_start);
        $day = $now->diffInDays($date);

        if ($day == 0) {
            return 1;
        }
        return $day;
    }

    /**
     * @param $token
     * @param $segment_id
     * @param $destination_name
     * @param $lng
     * @param $price
     * @param $lat
     * @return \Illuminate\Http\JsonResponse
     */
    public static function endSegment($token, $segment_id, $destination_name, $lng, $lat, $price = 0)
    {
        $request_type = 'transport';
        $segment = self::getCurrentSession();

        //TODO validation for activities
//        if (!self::validateActivity($token, $request_type)) {
//            return response()->json('error activity. you have a different activity in session', 400);
//        }

        $segment = Segment::find($segment_id);
        $segment->destination_name = $destination_name;
        $segment->destination_pos = $lat . ',' . $lng;
        $segment->price = $price;
        $segment->distance = GeolocationHelper::calculateDistance($segment);
        $segment->duration = GeolocationHelper::calculateDuration($segment);
        $points = array_merge(GeolocationHelper::parseLongLat($segment->origin_pos), [$lng, $lat]);

        dd($points);

        $segment->path = GeolocationHelper::encode($points);

        if ($segment->save()) {
            return response()->json('success', 200);
        }

        return response()->json('error', 403);
    }

    /**
     * @param $type
     * @param $id
     * @param $token
     */
    public static function newUserActivity($type, $id, $token)
    {
        Session::put($token . '.activity', [$type, $id]);
    }

    /**
     * resolves matching current activity
     * and end activity request type
     * @param $token
     * @param $request_type
     * @return null
     */
    public static function validateActivity($token, $request_type)
    {

        $activity = Session::get($token);

        if (!isset($activity['activity'])) {
            if (!$activity) {
                if (Auth::check()) {

                    $activity = self::getCurrentActivity();


                }

                return response()->json('login again..');
            }
            return response()->json('you dont have a current session.', 403);
        }

        $id = $activity['activity'][1];
        if ($activity == $request_type) {
            return $id;
        }

        return null;
    }

    /**
     * @param $user_id
     * @return mixed
     */
    public static function getCurrentSession($token)
    {
        $activity = Session::has($token.'.activity');

        if($activity==null){

            if(Auth::check()){
                $route = Auth::user()->iterinaries()->route();
                $segment = $route->segments()->orderBy('sequence','desc')->first();
                return $segment;
            }

            die(400);
        }

        $segment = ;
        $activity = $iterinary->activities();
        return $iterinary;
    }
}