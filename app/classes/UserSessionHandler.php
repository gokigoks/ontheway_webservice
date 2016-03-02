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
use App\Eat;
use App\User;
use App\Iterinary;
use DB;
use App\Route;
use App\Segment;
use App\Activity;
use App\Spot;
use App\FoodCategory;
use Carbon\Carbon;
use App\UserSession;
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

            $current_iterinary = $user->past_iterinaries()->with('route.segments')->first();
            $user->setAttribute('token', $token->uuid);
            $session = collect(['user' => $user]);
            $session->offsetSet('plannedIterinaries', $planned_iterinaries);
            $session->offsetSet('pastIterinaries', $past_iterinaries);
            $session->offsetSet('currentIterinary', $current_iterinary);
            //dd($session);
            self::startUserSession($user->id, $token->uuid);

            return ['body' => $session, 'http_code' => 200];
        } else {

            return ['body' => 'error bad credentials', 'http_code' => 400];
        }
    }

    /**
     * @param Request $request
     */


    /**
     * @param $token
     * @return bool
     */
    public static function check($token)
    {
        $session = UserSession::where('token', '=', $token)->first();

        if (!$session) return false;

        if ($session->count() > 0) {
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
//        $user = User::where('id','=',Session::get($token))->first();

        $session = UserSession::where('token', '=', $token)->first();

        if ($session == null) return response()->json('web token invalid', 403);

        $user = User::where('id', '=', $session->payload_id)->first();

        return $user;
    }

    /**
     * @param $token
     * @return mixed
     */
    public static function user($token)
    {
        $user = self::getByToken($token);

        return $user;
    }

    /**
     * @param $user
     * @return $iterinary
     */
    public static function getCurrentIterinary($user)
    {
        $iterinary = $user->current_iterinary()->with('route.segments')->first();
        return $iterinary;
    }

    public static function logout($token)
    {
        Session::forget($token);
    }

    /**
     * @param id
     * @param $token
     * @return Response
     */
    public static function startUserSession($id, $token)
    {
        //start a user session
//        Session::put($token, $id);
//        Session::save();

        $session = UserSession::firstOrNew(['payload_id' => $id]);
        $session->token = $token;
        $session->payload_id = $id;
        $session->save();

    }

    /**
     * @param $token
     * @param $iterinary
     */
    public static function addIterinarySession($token, $iterinary)
    {
        $current_session = Session::get($token);
        $current_session['iterinary'] = $iterinary->id;
        Session::put($token, $current_session);
    }

    /**
     * @param $token
     * @param $segment
     */
    public static function addSegmentSession($token, $segment)
    {
        $current_session = Session::get($token);
        $current_session['session'] = $segment->id;
        Session::put($token, $current_session);
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
        $user = self::getByToken($token);
        //dd($route->count());


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

        // activity
        $activity = new Activity();
        $activity->iterinary_id = $iterinary_id;
        $activity->day = self::getDiffInDays($token, $iterinary_id);
        $activity->start_time = Carbon::now()->toTimeString();

        // end activity

        $route->segments()->save($segment);
        $segment->activity()->save($activity);
        $iterinary = Iterinary::findOrFail($iterinary_id)->with('activities.typable')->first();
        //self::newUserActivity('transport', $segment->getAttribute('id'), $token);

        return response()->json($iterinary, 200);
    }

    /**
     * @param $token
     * @param $spot_name
     * @param $category
     * @param $lat
     * @param $lng
     * @param $iterinary_id
     * @return reponse json
     */
    public static function addSpot($token, $spot_name, $category, $lat, $lng, $iterinary_id)
    {
        //$categories = App\SpotCategory::list('');
        $spot_category = \DB::table('spot_categories')->select('main_cat', 'main_cat_id')->where('main_cat', 'LIKE', '%' . $category . '%')->distinct()->get();
        $activity = new Activity();

        $day = self::getDiffInDays($token, $iterinary_id);

        $spot = new Spot;

        $spot->main_category_id = $spot_category[0]->main_cat_id;
        $spot->place_name = $spot_name;
        $spot->lat = $lat;
        $spot->lng = $lng;

        $spot->pic_url = self::resolveCategoryPic($category);
        $spot->save();

        $start_time = Carbon::now()->toTimeString();
        $activity->start_time = $start_time;
        $activity->day = $day;
        $activity->iterinary_id = $iterinary_id;
        $activity->start_time = Carbon::now()->toTimeString();

        $spot->activity()->save($activity);
        $iterinary = Iterinary::findOrFail($iterinary_id)->with('activities.typable')->first();
        return response()->json($iterinary, 200);
    }//end

    /**
     * resolve pictures
     * @param $category
     * @return string
     */
    public static function resolveCategoryPic($category)
    {
        $pic_url = 'http://php-usjrproject.rhcloud.com/api/img/default.png';
        $categories = [
            'food' => 'http://php-usjrproject.rhcloud.com/api/img/food.png',
            'arts & entertainment' => 'http://php-usjrproject.rhcloud.com/api/img/arts.png',
            'event' => 'http://php-usjrproject.rhcloud.com/api/img/event.png',
            'nightlife spot' => 'http://php-usjrproject.rhcloud.com/api/img/night.png',
            'Outdoors & Recreation' => 'http://php-usjrproject.rhcloud.com/api/img/beach.png'
        ];

        foreach ($categories as $key => $item) {
            if ($key == $category) {
                $pic_url = $item;
            }
        }

        return $pic_url;
    }//end

    /**
     * @param $token
     * @param $place_name
     * @param $lng
     * @param $lat
     * @param $category
     * @param $iterinary_id
     * @return json response
     */
    public static function addFood($token, $place_name, $lng, $lat, $category, $iterinary_id)
    {

        $day = self::getDiffInDays($token, $iterinary_id);
        $foodcategory = DB::table('food_categories')->select('main_cat', 'main_cat_id')->distinct()->get();
        $activity = new Activity();
        $activity->start_time = Carbon::now()->toTimeString();
        $activity->iterinary_id = $iterinary_id;
        $activity->day = $day;

        $eat = new Eat();
        $eat->place_name = $place_name;
        $eat->pos = $lng . ',' . $lat;
        $eat->main_category_id = $foodcategory[0]->main_cat_id;
        $eat->sub_category_id = self::resolveFoodSubCat($category);
        $eat->pic_url = self::resolveCategoryPic('food');

        $eat->save();
        $eat->activity()->save($activity);
        $iterinary = Iterinary::findOrFail($iterinary_id)->with('activities.typable')->first();
        return response()->json($iterinary, 200);
    }

    /**
     * @param $token
     * @param $iterinary_id
     * @return mixed
     */
    public static function getDiffInDays($token, $iterinary_id)
    {
        $now = Carbon::now();
        $user = self::getByToken($token);

        $pivot = $user->iterinaries()->find($iterinary_id);

        $date_start = new Carbon($pivot->pivot->date_start);
        $day = $now->diffInDays($date_start);

        if ($day == 0 || $day > 20 ) {
            return 1; //default
        }

        return $day;
    }

    /**
     * @param $token
     * @param iterinary_id
     * @param $destination_name
     * @param $lng
     * @param $price
     * @param $lat
     * @return \Illuminate\Http\JsonResponse
     */
    public static function endSegment($token, $iterinary_id, $destination_name, $lng, $lat, $price = 0)
    {
        $iterinary = Iterinary::find($iterinary_id);
        $route = $iterinary->route;

        $segment = $route->segments()
            ->where('destination_name', '=', '')
            ->orWhere('destination_name', '=', 'null')
            ->first();

        $segment->destination_name = $destination_name;
        $segment->destination_pos = $lat . ',' . $lng;
        $segment->price = $price;
        $segment->distance = GeolocationHelper::calculateDistance($segment);
        $segment->duration = GeolocationHelper::calculateDuration($segment);
        $points = array_merge(GeolocationHelper::parseLongLat($segment->origin_pos), [$lng, $lat]);

        // update activity model
        $activity = $segment->activity;
        foreach ($activity as $item) {   //add ent time to activity
            $item->end_time = Carbon::now()->toTimeString();
            $item->save();
        }
        //

        $segment->path = GeolocationHelper::encode($points);


        if ($segment->save()) {
            $iterinary = Iterinary::findOrFail($iterinary_id)->with('activities.typable')->first();
            return response()->json($iterinary, 200);
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
        $activity = Session::get($token);
        $activity['type'] = $id;
        Session::put($token, $activity);
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
     * @param $token
     * @return mixed
     */
    public static function getCurrentActivity($token)
    {
        $session = self::getByToken($token);

        if ($session['activity'] == null) {
            if (Auth::check()) {
//                dd(Auth::user()->iterinaries()->get());
                $iterinary = Auth::user()->current_iterinary()->first();
                $route = $iterinary->route;
                $segment = $route->segments()->orderBy('sequence', 'desc')->first();
                return $segment;
            }
            die(400);
        }
        $segment = '';
        //$activity = $iterinary->activities();
//        return $iterinary;
    }

    public static function addActivity($id)
    {
        $activity = new Activity;
    }


    /**
     * @param $category
     * @return App\FoodCategory instance
     */
    public static function resolveFoodSubCat($category)
    {
        $category = FoodCategory::where('sub_cat', '=', $category)->first();

        $foodcategory = DB::table('food_categories')
            ->select('sub_cat_id')
            ->where('sub_cat', '=', $category)
            ->first();

        return $foodcategory;
    }


    public static function endFoodActivity($token, $iterinary_id, $price, $tips)
    {
        $user = self::getByToken($token);
        $current_iterinary = $user->iterinaries()->find($iterinary_id);
        $activity = $current_iterinary->activities()->food()->current()->first();
        $activity->end_time = Carbon::now()->toTimeString();
        $activity->save();

        $eat = $activity->typable;
        $eat->price = $price;
        $eat->tips = $tips;
        $eat->save();
        $iterinary = Iterinary::findOrFail($iterinary_id)->with('activities.typable')->first();
        return response()->json($iterinary, 200);
    }

    public static function endSpotActivity($token, $iterinary_id, $price, $tips)
    {
        $user = self::getByToken($token);
        $current_iterinary = $user->iterinaries()->find($iterinary_id);
        $activity = $current_iterinary->activities()->spot()->current()->first();
        $activity->end_time = Carbon::now()->toTimeString();
        $activity->save();
        $spot = $activity->typable;
        $spot->price = $price;
        $spot->tips = $tips;
        $spot->save();
        $iterinary = Iterinary::findOrFail($iterinary_id)->with('activities.typable')->first();
        return response()->json($iterinary, 200);
    }

    //
    public static function endIterinary($user,$iterinary)
    {
        $distance = 0;
        $duration = 0;
        $price = 0;

        $activities = $iterinary->activities;

        foreach ($activities as $activity) {
            $start_time = Carbon::parse($activity->start_time);
            $end_time = Carbon::parse($activity->end_time);
            $distance += GeolocationHelper::resolveDistance($activity);
            $duration += $end_time->diffInMinutes($start_time);
            $price += $activity->typable->price;
        }

        $iterinary->distance = $distance;
        $iterinary->duration = $duration;
        $iterinary->price = $price;
        $iterinary->save();

        event(new \App\Events\IterinaryWasCreated($user->id,$iterinary->id));

        return response()->json('success', 200);
    }

    /**
     * set iterinary start date
     * @param $token
     * @param $iterinary_id
     * @param $date
     * @return \Illuminate\Http\JsonResponse
     */
    public static function setIterinaryStartDate($token, $iterinary_id, $date)
    {
        $user = self::getByToken($token);

        $pivot_fields = [
            'date_start' => $date
        ];

        $user->iterinaries()->updateExistingPivot($iterinary_id, $pivot_fields, true);

        return response()->json('success', 200);
    }


}//end