<?php
/**
 * Created by PhpStorm.
 * User: eldringoks
 * Date: 2016-02-11
 * Time: 10:02 PM
 */

namespace App\Classes;

use App\Classes\tokenGenerator;
use App\Classes\FoursquareHelper;
use App\Classes\GeolocationHelper;
use App\Eat;
use App\OtherActivity;
use App\User;
use App\Iterinary;
use DB;
use App\Route;
use App\Segment;
use App\Hotel;
use App\Activity;
use App\Stop;
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
    public static function getCurrentIterinaryWithSegment($user)
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
    s     * @param $origin_name
     * @param $lng
     * @param $lat
     * @param $mode
     * @return \Illuminate\Http\JsonResponse
     */
    public static function addSegment($token, $origin_name, $lng, $lat, $mode)
    {   //todo
        $segment = new Segment;
        $segment->origin_name = $origin_name;
        $segment->origin_pos = $lat . ',' . $lng;
        $segment->mode = $mode;
        $iterinary = self::getUserCurrentIterinary($token);
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
        $activity->iterinary_id = $iterinary->id;
        $activity->day = self::getDiffInDays($token, $iterinary->id);
        $activity->start_time = Carbon::now()->toTimeString();

        // end activity

        $route->segments()->save($segment);
        $segment->activity()->save($activity);
        $iterinary = Iterinary::findOrFail($iterinary->id)->with('activities.typable')->first();
        //self::newUserActivity('transport', $segment->getAttribute('id'), $token);

        return response()->json($iterinary, 200);
    }

    /**
     * @param $token
     * @param $food_data
     * @param transpo
     * @return reponse json
     */
    public static function addSpot($token, $food_data, $transpo)
    {   //todo
        $response = self::resolveNewSegmentFromActivity($token, $transpo, $food_data);

        $current_iterinary = self::getUserCurrentIterinary($token);
        $day = self::getDiffInDays($token, $current_iterinary->id);
        $activity = new Activity();
        $activity->start_time = Carbon::now()->toTimeString();
        $activity->iterinary_id = $current_iterinary->id;
        $activity->day = $day;
        $eat = new Spot();
        $eat->place_name = $food_data['place_name'];
        $eat->lng = $food_data['lng'];
        $eat->lat = $food_data['lat'];
        $eat->tips = $food_data['review'];
        $eat->price = $food_data['price'];

        $foodcategory = FoursquareHelper::resolveSpotCategory($food_data['category']['cat_id']);

//        return response()->json($foodcategory['main_cat']);
        $eat->main_category_id = $foodcategory['main_cat'];
        $eat->sub_category_id = $foodcategory['sub_cat'];
        $pic = $food_data['category'];
        $eat->pic_url = $pic['prefix'] . '64' . $pic['suffix'];

//        return response()->json($eat);
        $eat->save();
        $eat->activity()->save($activity);

        $iterinary = Iterinary::findOrFail($current_iterinary->id)
            ->with('activities.typable')
            ->first();
        return response()->json($iterinary, 200);

//        $current_iterinary = self::getUserCurrentIterinary($token);
//        $day = self::getDiffInDays($token, $current_iterinary->id);
//        $activity = new Activity();
//        $activity->start_time = Carbon::now()->toTimeString();
//        $activity->iterinary_id = $current_iterinary->id;
//        $activity->day = $day;
//        $eat = new Spot();
//        $eat->place_name = $spot_data['place_name'];
//
//        $eat->lng = $spot_data['lng'];
//        $eat->lat = $spot_data['lat'];
//        $eat->tips = $spot_data['review'];
//        $eat->price = $spot_data['price'];
//
//        $spot_category = FoursquareHelper::resolveSpotCategory($spot_data['category']['cat_id']);
//
////        return response()->json($foodcategory['main_cat']);
//        $eat->main_category_id = $spot_category['main_cat'];
//        $eat->sub_category_id = $spot_category['sub_cat'];
//        $pic = $spot_data['category'];
//        $eat->pic_url = $pic['prefix'] . '64' . $pic['suffix'];
//
////        return response()->json($spot_category, 200);
//        self::resolveSegmentFromActivity($token);
//
////        return response()->json($eat);
//        $eat->save();
//        $eat->activity()->save($activity);
//
//
//        $iterinary = Iterinary::findOrFail($current_iterinary->id)
//            ->with('activities.typable')
//            ->first();
//        return response()->json($iterinary, 200);
//        return response()->json($eat);
    }//end

    /**
     * resolve pictures
     * @param $category
     * @param $type
     * @return string
     */
    public static function resolveCategoryPic($category, $type)
    {   //todo
//        $pic_url = 'http://php-usjrproject.rhcloud.com/api/img/default.png';
//        $categories = [
//            'food' => 'http://php-usjrproject.rhcloud.com/api/img/food.png',
//            'arts & entertainment' => 'http://php-usjrproject.rhcloud.com/api/img/arts.png',
//            'event' => 'http://php-usjrproject.rhcloud.com/api/img/event.png',
//            'nightlife spot' => 'http://php-usjrproject.rhcloud.com/api/img/night.png',
//            'Outdoors & Recreation' => 'http://php-usjrproject.rhcloud.com/api/img/beach.png'
//        ];

//        foreach ($categories as $key => $item) {
//            if ($key == $category) {
//                $pic_url = $item;
//            }
//        }
        $pic_url = 'http://php-usjrproject.rhcloud.com/api/img/default.png';
        if ($type == 'food') {
            $categories = FoursquareHelper::resolveFoodCategory($category);
            if (!$categories['sub_cat']) {

            }
        }


        return $pic_url;
    }//end

    /**
     * @param $token
     * @param $food_data
     * @param transpo
     * @return json response
     */
    public static function addFood($token, $food_data, $transpo)
    {
        //todo
        $response = self::resolveNewSegmentFromActivity($token, $transpo, $food_data);

        $current_iterinary = self::getUserCurrentIterinary($token);

        $day = self::getDiffInDays($token, $current_iterinary->id);
        $activity = new Activity();
        $activity->start_time = Carbon::now()->toTimeString();
        $activity->iterinary_id = $current_iterinary->id;
        $activity->day = $day;

        $eat = new Eat();
        $eat->place_name = $food_data['place_name'];
        $eat->lng = $food_data['lng'];
        $eat->lat = $food_data['lat'];
        $eat->tips = $food_data['review'];
        $eat->price = $food_data['price'];

        $foodcategory = FoursquareHelper::resolveFoodCategory($food_data['category']['cat_id']);

//        return response()->json($foodcategory['main_cat']);
        $eat->main_category_id = $foodcategory['main_cat'];
        $eat->sub_category_id = $foodcategory['sub_cat'];
        $pic = $food_data['category'];
        $eat->pic_url = $pic['prefix'] . '64' . $pic['suffix'];

//        return response()->json($eat);
        $eat->save();
        $eat->activity()->save($activity);
//        return $eat;

        $iterinary = Iterinary::findOrFail($current_iterinary->id)
            ->with('activities.typable')
            ->first();
        return response()->json($iterinary, 200);
//
//        $current_iterinary = self::getUserCurrentIterinary($token);
//        $day = self::getDiffInDays($token, $current_iterinary->id);
//        $activity = new Activity();
//        $activity->start_time = Carbon::now()->toTimeString();
//        $activity->iterinary_id = $current_iterinary->id;
//        $activity->day = $day;
//        $eat = new Eat();
//        $eat->place_name = $food_data['place_name'];
//        $eat->lng = $food_data['lng'];
//        $eat->lat = $food_data['lat'];
//        $eat->tips = $food_data['review'];
//        $eat->price = $food_data['price'];
//
//        $foodcategory = FoursquareHelper::resolveFoodCategory($food_data['category']['cat_id']);
//
////        return response()->json($foodcategory['main_cat']);
//        $eat->main_category_id = $foodcategory['main_cat'];
//        $eat->sub_category_id = $foodcategory['sub_cat'];
//        $pic = $food_data['category'];
//        $eat->pic_url = $pic['prefix'] . '64' . $pic['suffix'];
//
//        self::resolveSegmentFromActivity($token);
//
//        $eat->save();
//        $eat->activity()->save($activity);
//
//
//        $iterinary = Iterinary::findOrFail($current_iterinary->id)
//            ->with('activities.typable')
//            ->first();
//        return response()->json($iterinary, 200);
//
//        return response()->json($request);
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

        if ($day == 0 || $day > 20) {
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

    /**
     * @param $id
     */
    public static function addActivity($id)
    {
        $activity = new Activity;

    }


    /**
     * @param $category
     * @return FoodCategory instance
     */
    public static function resolveFoodSubCat($category)
    {
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
    public static function endIterinary($user, $iterinary)
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

        event(new \App\Events\IterinaryWasCreated($user->id, $iterinary->id));

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

    public static function getLastActivity($token)
    {
        $user = self::getByToken($token);
        $iterinary = $user->current_iterinary()->first();
        $activity = $iterinary
            ->activities()
            ->where('typable_type', '!=', 'App\\Segment')
            ->with('typable')
            ->orderBy('created_at', 'desc')
            ->first();
        if (!$activity) {
            $data = new \stdClass();
            $data->created_at = $iterinary->created_at;
            list($lng, $lat) = explode(',', $iterinary->origin_pos);
            $data->lng = $lng;
            $data->lat = $lat;
            $data->place_name = $iterinary->origin;
            return $data;
        }
        return $activity->typable;
    }

    public static function getLastSegment($iterinary)
    {
        $route = $iterinary->route;
        $segment = $route->segments()->orderBy('created_at', 'desc')->first();
        if (!$segment) return 'way sud ang segment';

        return $segment;
    }


    public static function resolveNewSegmentFromActivity($token, $travel_data, $new_activity)
    {
        $iterinary = self::getUserCurrentIterinary($token);
        if(!$iterinary)
        {
            $response = [
                'err code' => '403',
                'message' => 'no current iterinaries found for user'
            ];

            return $response;
        }

        $route = $iterinary->route;
        $last_activity = self::getLastActivity($token);
//        return $last_activity;
        $segment = new Segment();
//        return $last_activity;
        $segment->origin_name = $last_activity->place_name;
        $segment->origin_pos = $last_activity->lng . ',' . $last_activity->lat;
        $segment->details = $travel_data['transpo_details'];
        $segment->mode = $travel_data['mode'];
        $segment->day = self::getDiffInDays($token, $iterinary->id);
        $segment->destination_name = $new_activity['place_name'];
        $segment->destination_pos = $new_activity['lng'] . ',' . $new_activity['lat'];
        $segment->price = $travel_data['expense'];
        $segment->sequence = $route->segments()->count() + 1;
        $points = array_merge([$last_activity->lng, $last_activity->lat]
            , [$new_activity['lng'], $new_activity['lat']]);

        $segment->path = GeolocationHelper::encode($points);
        $segment->distance = GeolocationHelper::calculateDistance($segment);
        $segment->duration = GeolocationHelper::durationFromLastActivity($last_activity);

        $activity = new Activity();
        $activity->iterinary_id = $iterinary->id;
        $activity->day = self::getDiffInDays($token, $iterinary->id);
        $activity->end_time = Carbon::now()->toTimeString();
        $activity->start_time = $last_activity->created_at->toTimeString();
        // end activity

        // from end segment function
//        $segment->destination_name = $destination_name;
//        $segment->destination_pos = $lat . ',' . $lng;
//        $segment->price = $price;
//        $segment->distance = GeolocationHelper::calculateDistance($segment);
//        $segment->duration = GeolocationHelper::calculateDuration($segment);
//        $points = array_merge(GeolocationHelper::parseLongLat($segment->origin_pos), [$lng, $lat]);
//
//        $segment->path = GeolocationHelper::encode($points);
        //end from end segemtn function
        $route->segments()->save($segment);
        $segment->activity()->save($activity);

    }

    public static function getUserCurrentIterinary($token)
    {
        $user = self::getByToken($token);
        return $user->current_iterinary()->first();
    }

    /**
     * @param $token
     * @param $hotel_data
     * @param $transpo
     * @return array|\Illuminate\Http\JsonResponse
     */
    public static function addHotel($token, $hotel_data,$transpo)
    {

        $response = UserSessionHandler::resolveNewSegmentFromActivity($token, $transpo, $hotel_data);
//        return response()->json($response);

        if($response) return $response;

        $current_iterinary = UserSessionHandler::getUserCurrentIterinary($token);
        $day = UserSessionHandler::getDiffInDays($token, $current_iterinary->id);
        $activity = new Activity();
        $activity->start_time = Carbon::now()->toTimeString();
        $activity->iterinary_id = $current_iterinary->id;
        $activity->day = $day;
        $hotel = new Hotel();
        $hotel->place_name = $hotel_data['place_name'];
        $hotel->lng = $hotel_data['lng'];
        $hotel->lat = $hotel_data['lat'];
        $hotel->tips = $hotel_data['review'];
        $hotel->price = $hotel_data['price'];


        $hotel->pic_url = '';
//        return response()->json($eat);
        $hotel->save();
        $hotel->activity()->save($activity);


        $iterinary = Iterinary::findOrFail($current_iterinary->id)
            ->with('activities.typable')
            ->first();
        return response()->json($iterinary, 200);
    }

    //stops
    public static function addStop($token,$stop_data,$transpo)
    {
        $response = UserSessionHandler::resolveNewSegmentFromActivity($token, $transpo, $stop_data);
//        return response()->json($response);

        $current_iterinary = UserSessionHandler::getUserCurrentIterinary($token);
        $day = UserSessionHandler::getDiffInDays($token, $current_iterinary->id);
        $activity = new Activity();
        $activity->start_time = Carbon::now()->toTimeString();
        $activity->iterinary_id = $current_iterinary->id;
        $activity->day = $day;
        $stop = new Stop();
        $stop->place_name = $stop_data['place_name'];
        $stop->lng = $stop_data['lng'];
        $stop->lat = $stop_data['lat'];
        $stop->details = $stop_data['details'];
        $stop->price = $stop_data['price'];

//        return response()->json($eat);
        $stop->save();
        $stop->activity()->save($activity);


        $iterinary = Iterinary::findOrFail($current_iterinary->id)
            ->with('activities.typable')
            ->first();
        return response()->json($iterinary, 200);
    }

    public static function addOtherActivity($token,$other_data,$transpo)
    {
        $response = UserSessionHandler::resolveNewSegmentFromActivity($token, $transpo, $stop_data);
//        return response()->json($response);

        $current_iterinary = UserSessionHandler::getUserCurrentIterinary($token);
        $day = UserSessionHandler::getDiffInDays($token, $current_iterinary->id);
        $activity = new Activity();
        $activity->start_time = Carbon::now()->toTimeString();
        $activity->iterinary_id = $current_iterinary->id;
        $activity->day = $day;
        $stop = new OtherActivity();
        $stop->name = $other_data['place_name'];
        $stop->lng = $other_data['lng'];
        $stop->lat = $other_data['lat'];
        $stop->review = $other_data['review'];
        $stop->expense = $other_data['expense'];

//        return response()->json($eat);
        $stop->save();
        $stop->activity()->save($activity);


        $iterinary = Iterinary::findOrFail($current_iterinary->id)
            ->with('activities.typable')
            ->first();
        return response()->json($iterinary, 200);
    }


}//end