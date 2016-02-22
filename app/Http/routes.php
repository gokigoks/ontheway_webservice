<?php

/*
|--------------------------------------------------------------------------
| Application Routes
-|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'WelcomeController@index');

Route::get('home', 'HomeController@index');

//auth for web logins and register
Route::controllers([
    'auth' => 'Auth\AuthController',
    'password' => 'Auth\PasswordController',
]);
Route::group(['middleware' => 'auth'], function () {

    Route::get('test/foursquare', 'ApiController@get_foursquare');
    Route::get('test/rome2rio', 'ApiController@get_rome2rio');


    Route::get('test/api/dump', 'ApiController@testAjax');

    Route::get('test/api/users', 'ApiController@get_users');
});

Route::post('test/foursquare', 'ApiController@post_foursquare');
Route::post('test/rome2rio', 'ApiController@post_rome2rio');

Route::post('user/logout', ['middleware' => 'login', 'uses' => 'UserSessionController@logout']);

Route::get('user/login', ['middleware' => 'login', 'uses' => function () {
    // cross site route. do nothing for get request
}]);
Route::post('user/login', ['middleware' => 'login', 'uses' => 'UserSessionController@login']);
Route::post('user/register', ['middleware' => 'login', 'uses' => 'UserSessionController@register']);
Route::post('test/login', ['middleware' => 'login', 'uses' => function () {
    $credentials = array(
        'email' => Input::get('email'),
        'password' => Input::get('password'),
    );
    if (Auth::attempt($credentials)) {
        return response()->json('logged in ', 200);
    }
    return response()->json('error credentials', 200);
}]);

/*
*   API SERVICE ROUTES
*
**/

Route::get('get/hotels', 'ApiController@get_hotels');

Route::get('pingServer', ['middleware' => 'cors', function () {
    /*
    *   This is a test route
    *
    **/
    return response()->json('server up', 200);

}]);

Route::post('pingServer', ['middleware' => 'cors', function () {
    /*
    *   This is a test route
    *
    **/

    $credentials = array(
        'email' => Input::get('email'),
        'password' => Input::get('password'),
    );

    return response()->json($credentials, 200);

}]);

Route::group(['prefix' => 'api', 'middleware' => 'cors'], function () {

    header('Access-Control-Allow-Origin', '*');
    header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE');
    header('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With');
    header('Access-Control-Max-Age', '28800');

    Route::group(['prefix' => 'rome2rio'], function () {
        Route::post('search', 'ApiController@post_rome2rio');

        Route::get('search', 'ApiController@get_rome2rio');
    });
    Route::group(['prefix' => 'foursquare'], function () {
        Route::post('search', 'ApiController@post_foursquare');
    });
});
/*
*   test helper classes
*/
Route::get('helperfile/rome2rio', 'ApiController@test_rome2rio_helper');
Route::get('helperfile/foursquare', 'ApiController@foursquare');

//


//test route for getting airport long lat using area codes
Route::get('rome2rio/autocomplete', function () {
    $url = "http://free.rome2rio.com/api/1.2/json.Autocomplete?key=&query=";
});

Route::get('gmaps/getairport', function () {

    //http://maps.googleapis.com/maps/api/geocode/json?address=airport%20Dubai&sensor=false

    $origin = 'CEB';
    $destination = 'MNL';
    /**
     * $url = API url
     * kani ray ilisi earl
     */
    $url = "http://free.rome2rio.com/api/1.2/json/Search?key=nKYL3BZS&oName=" . $origin . "&dName=" . $destination;

    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $data = curl_exec($ch);

    $data = json_decode($data);
    dd($data, $ch);
    return response()->json($data, 200);

    curl_close($ch);
});

Route::get('rome2rio', function () {

    $url = 'http://free.rome2rio.com/api/1.2/json/Search?key=nKYL3BZS&oName=Cebu&dName=Manila';
    $content = file_get_contents($url);
    $json = json_decode($content, true);

    dd($json);
});

use Illuminate\Database\Seeder;
use \App\Spot;
use Faker as Faker;

Route::get('test/seed', function () {
    DB::table('spots')->delete();
    $faker = Faker\Factory::create();

    for ($i = 0; $i < 5; $i++) {
        $spot = new Spot;
        $spot->place_name = $faker->streetName;
        $spot->pos = $faker->latitude . "," . $faker->longitude;
        $spot->price = $faker->randomNumber(2);
        $spot->tips = $faker->text(20);
        $spot->save();
    }
});

// Home Page Controllers

Route::get('user/iterinary', 'IterinaryController@getIterinaries');
Route::post('user/iterinary', 'IterinaryController@create');
Route::get('user/iterinary/{id}', 'IterinaryController@showIterinary');


Route::get('api/recomendee/getrecommend', ['middleware' => 'cors', 'uses' => 'RecommenderController@get_recommend']);
Route::get('api/addspots', ['middleware' => 'cors', 'uses' => 'RecommenderController@add_spots']);
Route::get('api/getrecommend', ['middleware' => 'cors', 'uses' => 'RecommenderController@get_recommend']);

// Geolocation end points
Route::post('api/geolocation/encode', 'GeolocationController@encode');
Route::post('api/geolocation/decode', 'GeolocationController@decode');
Route::post('api/geolocation/pointsToPath', 'GeolocationController@addPointsToPath');
Route::post('api/geolocation/pathToPath', 'GeolocationController@addPathToPath');
//


// End Recomendee Iterinaries

/**
 *    Recommendation Controllers
 *
 * @uses  App\Http\Controllers\RecommenderController
 * @example   description
 **/
Route::get('api/iterinary/', 'RecommenderController@getTripRecommendation');
//Route::get('api/spots/','RecommenderController@getSpotRecommendation');
//  End Recommendation 


// SPOTS
Route::get('api/spots', 'SpotController@getSpots');
Route::post('api/spots/add', 'SpotController@newSpot');
Route::post('api/spots/end', 'SpotController@endSpot');
// END SPOTS


//Route::get('api/hotels','')

// --   GEOLOCATION ROUTES -- //
Route::get('geolocationhelper', function () {
    $longlat = "12.4221,38.9888";
    $path = 'a}pwAwgraVcAZgDvD?V|BvG?bBm@z@sPrKuCrCiBvCyCfH~RjFnBZzGbBxB?vDgBnB?f@^dA?tEgBZk@]_DcB_BgACsEfCi@fD}BbB{Br@aAC{^kJwGrP[rAql@v@eAVoPvECbATnLSKgI?gSnAk^kW]OE{B';
    //$data =  App\Classes\Geolocationhelper::parseLongLat($longlat);
    // $data = App\Classes\Geolocationhelper::decode($path);
    // $asd = App\Classes\Geolocationhelper::pair($data);
    // $newpath = App\Classes\GeolocationHelper::encode($asd);
    $rome2riodata = \Rome2RioData::call();
    $airports = App\Classes\GeolocationHelper::getAirportLongLat($rome2riodata);
    dd($airports);
});

// --   END GEOLOCATION ROUTES -- //

// -- FOURSQUARE ROUTES -- //`
Route::get('testcache', function () {
    $data = \App\Classes\Rome2rioHelper::call();
    //Cache::add('cebu,manila',$data,20);
    dd($data);
});
//

//  --  SEGMENT ROUTES -- //
Route::get('api/iterinary/segments/all', ['middleware' => 'cors', 'uses' => 'SegmentController@getAll']);
Route::get('api/iterinary/segments/show', ['middleware' => 'cors', 'uses' => 'SegmentController@showSegment']);
Route::post('api/iterinary/segments/add', ['middleware' => 'cors', 'uses' => 'SegmentController@addNew']);
Route::post('api/iterinary/segments/end', ['middleware' => 'cors', 'uses' => 'SegmentController@endSegment']);

//  -- END SEGMENT ROUTES -- //

//  -- STOPS ROUTES -- //
Route::get('api/iterinary/stops/get', ['middleware' => 'cors', 'uses' => 'IterinaryController@getStops']);
Route::post('api/iterinary/stops/add', ['middleware' => 'cors', 'uses' => 'SegmentController@addStop']);
//  -- END STOP ROUTES -- //

//  -- POPULATE TABLES -- //
Route::get('populate/routes', ['middleware' => 'cors', 'uses' => 'TestController@populateRoutes']);
Route::get('populate/spots', ['middleware' => 'cors', 'uses' => 'TestController@populateSpots']);
Route::get('populate/eats', ['middleware' => 'cors', 'uses' => 'TestController@populateEats']);
Route::get('populate/categories', 'TestController@populateCategories');
//  -- END -- //

Event::listen('cache.hit', function ($key, $value) {
    //var_dump($key, $value);
});
//  -- test helpers -- //
Route::get('test/helpers', function () {

    App\Classes\FoursquareHelper::testHelper();
    App\Classes\Rome2rioHelper::testHelper();
    App\Classes\GeolocationHelper::testHelper();

});

Route::get('test/distance', function () {

    $lnglat1 = App\Classes\GeolocationHelper::parseLongLat(Input::get('lnglat1'));
    //$lnglat2 = Input::get('lnglat2');
    $segment = new App\Segment;
    $segment->origin_pos = $lnglat1;
    //$segment->destination_pos = $lnglat2;
    //dd($lnglat1);
    $spots = App\Spot::haversine($lnglat1[0], $lnglat1[1])->get();
    //return response()->json(App\Classes\GeolocationHelper::calculateDistance($segment),200);
    dd($spots);
});

Event::listen('cache.hit', function ($query) {
    var_dump('cache accessed ' . $query);
});

Route::get('api/img/{img_url}', 'ApiController@imageHandler');

Route::get('api/image/{img}', function ($img) {
    return response()->make(file_get_contents(public_path() . '/images/' . $img))->header('Content-Type', 'png');
});
// -- end test helpers -- //

Route::get('test/collection', function () {


    //if(Session::forget('56bdc5c7a38ab')) return response()->json('forgottten',200);
    //Session::flush();
//    $category = Input::get('category');
//    $categories  = \DB::table('spot_categories')->select('main_cat','main_cat_id')->where('main_cat','=',$category)->distinct()->get();
//    dd($categories[0]->main_cat_id);
    

    Session::put('56c246e707517.activity', 'some activity');
    $array = Session::get('56c246e707517');
    $array = array_merge($array, ['new' => 'new activity']);
    Session::put('56c246e707517', $array);

    dd(Session::all(), $array, Session::get('56c246e707517.new'));
    // return response()->json($data, 200);
});

Route::get('flush/session', function () {
    if (Input::get('token') == 'gokigoks') {
        Session::flush();
    }
});


Route::get('checksession', function () {
    $token = Input::get('token');

    return response()->json(App\Classes\UserSessionHandler::check($token), 200);
});

Route::get('iterinary/assign', function () {
    $user_id = Input::get('user');
    $iterinary_id = Input::get('iterinary');
    $status = (!Input::get('status')) ? 'doing' : Input::get('status');
    $user = App\User::find($user_id);
    $iterinary = App\Iterinary::find($iterinary_id);

    $user->iterinaries()->attach($iterinary->id, ['status' => $status, 'date_start' => Carbon\Carbon::now()]);
});

Route::get('checktoken', function () {
    $token = Input::get('token');
    return Session::has($token);
});

Route::get('days/getdiff', function () {
    $user = App\User::find(Input::get('user_id'));
    $iterinary_id = Input::get('iterinary_id');
    $now = Carbon\Carbon::now();
    $start_date = Carbon\Carbon::now()->subDays(2);
    $pivot = $user->iterinaries()->find($iterinary_id);
    $date = new Carbon($pivot->pivot->date_start);
    $day = $now->diffInDays($date);

    dd($day, $start_date, $now);
});

Route::get('currentactivity', function () {
    $token = Input::get('token');
    $activity = Session::get($token);

    dd($activity);
});

Route::get('createsession', function () {
    $token = Input::get('token');
    $activity = Input::get('activity_type');
    $activity_id = Input::get('activity_id');
    $activity = [Input::get('activity_type') => Input::get('activity_id')];
    $all = Session::all();
    $current = Session::get($token);
    $activity_session = Session::put($token . '.activity', $activity);

    $result = Session::get($token);
    dd($result);

});

Route::get('api/recommend/search', ['middleware' => 'cors', 'uses' => 'RecommenderController@getIterinaryRecommendation']);
//Route::get('');

// Recomendee Iterinaries
Route::get('api/iterinary/current', ['middleware' => 'cors', 'uses' => 'IterinaryController@getCurrent']);
Route::get('api/iterinary/all', ['middleware' => 'cors', 'uses' => 'IterinaryController@getAll']);
Route::get('api/iterinary/planned', ['middleware' => 'cors', 'uses' => 'IterinaryController@getPlanned']);
Route::get('api/iterinary/past', ['middleware' => 'cors', 'uses' => 'IterinaryController@getPast']);
Route::post('api/iterinary/startplanned', ['middleware' => 'cors', 'uses' => 'IterinaryController@startPlannedIterinary']);

Route::get('api/iterinary/getpath', ['middleware' => 'cors', 'uses' => 'IterinaryController@getPath']);
Route::get('api/route/get', ['middleware' => 'cors', 'uses' => 'IterinaryController@getRoute']);
Route::post('api/iterinary/follow', ['middleware' => 'cors', 'uses' => 'IterinaryController@copyIterinary']);

// Activity routes
Route::post('plot/iterinary/activity/new', ['middleware' => 'cors', 'uses' => 'ActivityController@new']);
Route::post('plot/iterinary/activity/get', ['middleware' => 'cors', 'uses' => 'ActivityController@get']);
// END activity routes


// CONTRIBUTOR ITERINARIESadd
Route::post('plot/iterinary/new', ['middleware' => 'cors', 'uses' => 'IterinaryController@newIterinary']);
Route::post('plot/iterinary/end', ['midtdleware' => 'cors', 'uses' => 'IterinaryController@endIterinary']);
Route::post('plot/iterinary/addactivity', ['middleware' => 'cors', 'uses' => 'ActivityController@addActivity']);
Route::post('plot/iterinary/endactivity', ['middleware' => 'cors', 'uses' => 'ActivityController@endActivity']);
// END CONTRIBUTOR  ITERINARIES




