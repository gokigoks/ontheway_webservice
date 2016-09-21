<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Classes\Rome2rioHelper as Rome2Rio;
use App\Classes\FoursquareHelper as Foursquare;
use App\Classes\UserSessionHandler;
use Carbon\Carbon;
use App\Iterinary;
use App\Activity;
use App\Route;
use App\Segment;
use App\Stop;
use App\Hotel;
use App\Spot;
use App\Eat;
use App\User;
use Input;
use Cache;
use Illuminate\Http\Request;

class TestController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function populateRoutes()
    {
        $inputs = [
            'origin' => rawurlencode(Input::get('origin')),
            'destination' => rawurlencode(Input::get('destination')),
            'pax' => Input::get('pax')
        ];

        $airports = [];
        $user_id = Input::get('user_id');
        $contributor = User::find($user_id);

        $now = Carbon::now();

        if ($contributor == null) {
            return response()->json('user not found.', 404);
        }

        $data = Rome2Rio::call($inputs['origin'], $inputs['destination']);

        if (isset($data->airports)) {
            foreach ($data->airports as $airport) {
                $airports[$airport->code] = $airport->pos;
            }
        }

        foreach ($data->routes as $route) {
            $iterinary = new Iterinary();
            $iterinary->origin = $data->places[0]->name;
            $iterinary->destination = $data->places[1]->name;
            $iterinary->creator_id = $contributor->id;
            $iterinary->duration = $route->duration;
            $iterinary->distance = $route->distance;

            $iterinary->price = Rome2Rio::getRome2RioPrice($route);

            $new_route = new Route();
            $new_route->name = $route->name;
            $new_route->distance = $route->distance;
            $new_route->duration = $route->duration;
            $new_route->price = Rome2Rio::getRome2RioPrice($route);
            $new_route->save();


            $iterinary->route()->associate($new_route);
            $iterinary->save();
            $contributor->iterinaries()->attach($iterinary->id, ['date_start' => $now]);
            $i = 1;
            foreach ($route->segments as $segment) {

                $new_segment = new Segment();

                if ($segment->kind == "flight") {
                    $segment = Rome2Rio::convertToFlightSegment($segment, $data);
                }

                $new_segment->mode = (!isset($segment->subkind)) ? $segment->kind : $segment->subkind;
                $new_segment->sequence = $i;
                $new_segment->origin_name = (!isset($segment->sName)) ? "" : $segment->sName;
                $new_segment->destination_name = (!isset($segment->tName)) ? "" : $segment->tName;;
                $new_segment->origin_pos = $segment->sPos;
                $new_segment->destination_pos = $segment->tPos;
                $new_segment->price = Rome2Rio::getRome2RioPrice($segment);
                $new_segment->path = ($segment->kind == "flight") ? Rome2Rio::getFlightPath($airports[$segment->sCode], $airports[$segment->tCode]) : $segment->path;
                $new_segment->distance = $segment->distance;
                $new_segment->duration = $segment->duration;

                $new_route->segments()->save($new_segment);

                $activity = new Activity();
                $activity->iterinary_id = $iterinary->id;
                $activity->day = 1;
                $activity->start_time = Carbon::now()->toTimeString();
                $activity->end_time = Carbon::now()->addMinute($segment->duration)->toTimeString();

                $new_segment->activity()->save($activity);

                $i++;
            }

            unset($i); // unset index for segments sequence
        }
        //dd($data);

    }

    /**
     * populate spots database
     *
     * @return Response
     */
    public function populateSpots()
    {
        $ll = Input::get('ll');
        $query_type = Input::get('query');
        if ($query_type == null) $query_type = "beach";
        $data = Foursquare::call($query_type, $ll);

        $spots = (!isset($data->response->venues)) ? null : $data->response->venues;
        if ($spots) {
            foreach ($spots as $spot) {

                $new_spot = new Spot();
                $new_spot->place_name = $spot->name;
                $new_spot->pic_url = Foursquare::getImage($spot);
                $new_spot->lat = $spot->location->lat;
                $new_spot->lng = $spot->location->lng;
                $new_spot->price = 50 * rand(1, 4);
                $new_spot->save();

            }
        } else {
            return response()->json('your spots query was empty.. try again fo!', 400);
        }

        return response()->json('success', 200);
    }


    public function populateEats()
    {
        $ll = Input::get('ll');
        $query_type = Input::get('query');
        $data = Foursquare::call($query_type, $ll);

        $spots = (!isset($data->response->venues)) ? null : $data->response->venues;
        if ($spots) {
            foreach ($spots as $spot) {
                $new_spot = new Eat();
                $new_spot->place_name = "name";
                $new_spot->pic_url = Foursquare::getImage($spot);
                $new_spot->lat = $spot->location->lat;
                $new_spot->lng = $spot->location->long;
                $new_spot->price = 125 * rand(1, 4);
                $new_spot->save();
            }
        } else {
            return response()->json('your food query was empty, fo!', 400);
        }

    }

    /**
     *
     * @return mixed
     */
    public function populateCategories()
    {
        $refresh = Input::get('refresh');
        if ($refresh == true) {
            Cache::forget('categories');
        }
        if (Cache::has('categories')) {
            $data = Cache::get('categories');

            Foursquare::saveSpotCategories($data->response->categories);

        } else {

            $ch = curl_init();
            $url = 'https://api.foursquare.com/v2/venues/categories?&oauth_token=1MZTZYIARGVDAGDQAHOVESDUR3P4OFZA2ABTIBESMJNNJM0T&v=20160106';
            curl_setopt($ch, CURLOPT_URL, $url);
            //https://api.foursquare.com/v2/venues/search?ll=10.3156990,123.8854370&oauth_token=1MZTZYIARGVDAGDQAHOVESDUR3P4OFZA2ABTIBESMJNNJM0T&v=20160106&query=food
            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $data = curl_exec($ch);

            if (curl_errno($ch)) {
                die("Couldn't send request: " . curl_error($ch));
            } else {

                $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

                if ($resultStatus == 200) {
                    $expiry = Carbon::now()->addDays(1);
                    $data = json_decode($data);
                    Cache::add('categories', $data, $expiry);
                    Foursquare::saveSpotCategories($data->response->categories);
                    return response()->json('success', 200);
                } else {

                    die('Request failed: HTTP status code: ' . $resultStatus);
                }
            }
        }

        // dd(json_decode($data));
    }

    public function addFoodTest(Request $request)
    {
        $request = $request->all();
        return response()->json($request);
        $food_data = $request['food'];
        $token = $request['token'];
        $current_iterinary = UserSessionHandler::getUserCurrentIterinary($token);
        $day = UserSessionHandler::getDiffInDays($token, $current_iterinary->id);
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

        $foodcategory = Foursquare::resolveFoodCategory($food_data['category']['cat_id']);

//        return response()->json($foodcategory['main_cat']);
        $eat->main_category_id = $foodcategory['main_cat'];
        $eat->sub_category_id = $foodcategory['sub_cat'];
        $pic = $food_data['category'];
        $eat->pic_url = $pic['prefix'] . '64' . $pic['suffix'];

        UserSessionHandler::resolveSegmentFromActivity($token);

        $eat->save();
        $eat->activity()->save($activity);


        $iterinary = Iterinary::findOrFail($current_iterinary->id)
            ->with('activities.typable')
            ->first();
        return response()->json($iterinary, 200);
//        return response()->json($eat);

        return response()->json($request);
    }


    //SPOT
    //id
    //place_name
    //pic_url
    //lat
    //lng
    //price
    //main_category_id
    //sub_category_id
    //tips

    public function addSpotTest(Request $request)
    {

        $request = $request->all();
        $food_data = $request['spot'];
        $token = $request['token'];
        $transpo = $request['transpo'];

        $response = UserSessionHandler::resolveNewSegmentFromActivity($token, $transpo, $food_data);

//        return response()->json($response);

        $current_iterinary = UserSessionHandler::getUserCurrentIterinary($token);
        $day = UserSessionHandler::getDiffInDays($token, $current_iterinary->id);
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

        $foodcategory = Foursquare::resolveSpotCategory($food_data['category']['cat_id']);

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
//        return response()->json($eat);

    }

    public function addHotelTest(Request $request)
    {
        $request = $request->all();
        $food_data = $request['hotel'];
        $token = $request['token'];
        $transpo = $request['transpo'];

        $response = UserSessionHandler::resolveNewSegmentFromActivity($token, $transpo, $food_data);

//        return response()->json($response);

        $current_iterinary = UserSessionHandler::getUserCurrentIterinary($token);
        $day = UserSessionHandler::getDiffInDays($token, $current_iterinary->id);
        $activity = new Activity();
        $activity->start_time = Carbon::now()->toTimeString();
        $activity->iterinary_id = $current_iterinary->id;
        $activity->day = $day;
        $hotel = new Hotel();
        $hotel->hotel_name = $food_data['place_name'];
        $hotel->lng = $food_data['lng'];
        $hotel->lat = $food_data['lat'];
        $hotel->tips = $food_data['review'];
        $hotel->price = $food_data['price'];


        $hotel->pic_url = '';
//        return response()->json($eat);
        $hotel->save();
        $hotel->activity()->save($activity);


        $iterinary = Iterinary::findOrFail($current_iterinary->id)
            ->with('activities.typable')
            ->first();
        return response()->json($iterinary, 200);
    }

    public function addTranspoTest(Request $request)
    {
        $request = $request->all();
        return response()->json($request);
    }

    public function newIterinaryTest(Request $request)
    {
        $request = $request->all();
        return response()->json($request);
    }


    public function getUserIP()
    {
        $client  = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote  = $_SERVER['REMOTE_ADDR'];

        if(filter_var($client, FILTER_VALIDATE_IP))
        {
            $ip = $client;
        }
        elseif(filter_var($forward, FILTER_VALIDATE_IP))
        {
            $ip = $forward;
        }
        else
        {
            $ip = $remote;
        }

        return $ip;
    }

    //TODO
    public function testGeoLocationPhp(Input $input)
    {
//        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        $user_ip = $this->getUserIP();
        //freegeoip.net/json
//        $data = json_decode(file_get_contents("http://freegeoip.net/json/{$ip}"));
        $data = json_decode(file_get_contents("http://ipinfo.io/{$user_ip}/json"));
        if(!isset($data->loc) )
        {
          return redirect()->to('test/geolocation')->with(['message' => 'we cant determine your location.','data' => (array)$data]);
        }
        $ll = $data->loc;
        $keyword = $input->get('keyword');
        $data = Foursquare::call($keyword, $ll);
        $data = $data->response->venues;
        return view('results',compact('data'));

    }
    public function testGeoLocation(Input $input)
    {
        $latitude = $input->get('latitude');
        $longitude = $input->get('longitude');
        $ll = $latitude.','.$longitude;
        $keyword = $input->get('keyword');
//        $ip = $_SERVER['REMOTE_ADDR'];

        $data = Foursquare::call($keyword, $ll);
        $data = $data->response->venues;

        return view('results',compact('data'));
//        $details = json_decode(file_get_contents("http://ipinfo.io/{$ip}/json"));
        dd($data);
//        $location = file_get_contents('http://freegeoip.net/json/'.$_SERVER['REMOTE_ADDR']);
//        dd($ip,$details);
    }


}
