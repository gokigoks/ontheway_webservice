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

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);
Route::group(['middleware' => 'auth'], function(){

    Route::get('test/foursquare','ApiController@get_foursquare');
    Route::get('test/rome2rio','ApiController@get_rome2rio');

    Route::post('test/foursquare','ApiController@post_foursquare');
    Route::post('test/rome2rio','ApiController@post_rome2rio');

    Route::get('test/api/dump', 'ApiController@testAjax');

    Route::get('test/api/users', 'ApiController@get_users');
});


Route::get('user/logout', ['middleware' => 'cors', 'uses' => function()
{

   if(Auth::check()){
        Auth::logout();
        return response()->json('user logged out..',200);
   }
   else
   {
        return response()->json('user wasnt even logged in, dummy!',401);
   }
   
}]);

Route::post('user/login', ['middleware' => 'cors', 'uses' => function()
{           
    $credentials = array(
        'email' => Input::get('email'), 
        'password' => Input::get('password'),            
    );

    if (Auth::attempt( $credentials ))
    {   
        $user_object = json_encode(Auth::user());
        return Response::json($user_object,200);        
    }
    else{        
        return Response::json("error.. bad credentials", 400);    
    }
}]);


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

Route::post('pingServer',['middleware' => 'cors', function(){
    /*
    *   This is a test route
    *
    **/ 

    $credentials = array(
        'email' => Input::get('email'), 
        'password' => Input::get('password'),            
    );

    return response()->json($credentials,200);

}]);

Route::group(['prefix'=>'api', 'middleware' => 'cors'], function(){
    
    header('Access-Control-Allow-Origin' , '*');         
    header('Access-Control-Allow-Methods', 'POST, GET, OPTIONS, PUT, DELETE');
    header('Access-Control-Allow-Headers', 'Content-Type, Accept, Authorization, X-Requested-With');
    header('Access-Control-Max-Age', '28800');

    Route::group(['prefix'=>'rome2rio'], function(){
        Route::post('search', 'ApiController@post_rome2rio');

        Route::get('search', 'ApiController@get_rome2rio');
    });
    Route::group(['prefix'=>'foursquare'], function(){
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
Route::get('rome2rio/autocomplete', function()
{
    $url = "http://free.rome2rio.com/api/1.2/json.Autocomplete?key=&query=";
});

Route::get('gmaps/getairport',function(){

    //http://maps.googleapis.com/maps/api/geocode/json?address=airport%20Dubai&sensor=false

        $origin = 'CEB';
        $destination  = 'MNL';
        /**
         * $url = API url
         * kani ray ilisi earl
         */
        $url = "http://free.rome2rio.com/api/1.2/json/Search?key=nKYL3BZS&oName=".$origin."&dName=".$destination;

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);

        $data = json_decode($data);
        dd($data,$ch);
        return response()->json($data,200);
                
        curl_close($ch);
});

Route::get('rome2rio', function(){

    $url = 'http://free.rome2rio.com/api/1.2/json/Search?key=nKYL3BZS&oName=Cebu&dName=Manila';
    $content = file_get_contents($url);
    $json = json_decode($content, true);

    dd($json);
});

use Illuminate\Database\Seeder;
use \App\Spot;
use Faker as Faker;

Route::get('test/seed', function(){
    DB::table('spots')->delete();
        $faker = Faker\Factory::create();

        for($i=0; $i<5;$i++){
            $spot = new Spot;
          $spot->place_name = $faker->streetName;
          $spot->pos = $faker->latitude.",".$faker->longitude;
          $spot->price = $faker->randomNumber(2);
          $spot->tips = $faker->text(20);
          $spot->save();
        }
});

// Home Page Controllers

Route::get('user/iterinary','IterinaryController@getIterinaries');

Route::post('user/iterinary','IterinaryController@create');

Route::get('user/iterinary/{id}','IterinaryController@showIterinary');


Route::get('api/recomendee/getrecommend', ['middleware' => 'cors', 'uses' => 'RecommenderController@get_recommend']);
Route::get('api/addspots',['middleware' => 'cors', 'uses' => 'RecommenderController@add_spots']);
Route::get('api/getrecommend',['middleware' => 'cors', 'uses' => 'RecommenderController@get_recommend']);

// Geolocation end points
Route::post('api/geolocation/encode','GeolocationController@encode');
Route::post('api/geolocation/decode','GeolocationController@decode');
Route::post('api/geolocation/pointsToPath','GeolocationController@addPointsToPath');
Route::post('api/geolocation/pathToPath','GeolocationController@addPathToPath');
//


// CONTRIBUTOR ITERINARIESadd
Route::post('plot/iterinary/new',['middleware' => 'cors', 'uses' => 'IterinaryController@newIterinary']);
Route::post('plot/iterinary/end',['midtdleware' => 'cors', 'uses' => 'IterinaryController@end']);
Route::post('api/iterinary/addactivity',['middleware' => 'cors', 'uses' => 'ActivityController@addActivity']);
// END CONTRIBUTOR  ITERINARIES

// Recomendee Iterinaries
Route::get('api/iterinary/planned',['middleware' => 'cors', 'uses' => 'IterinaryController@getPlanned']);
Route::get('api/iterinary/past',['middleware' => 'cors', 'uses' => 'IterinaryController@getPast']);
Route::get('api/iterinary/current',['middleware' => 'cors', 'uses' => 'IterinaryController@getCurrent']);
// End Recomendee Iterinaries

/**
*    Recommendation Controllers
* 
* @uses  App\Http\Controllers\RecommenderController
* @example   description
**/
Route::get('api/iterinary/','RecommenderController@getTripRecommendation');
Route::get('api/spots/','RecommenderController@getSpotRecommendation');
//  End Recommendation 


// SPOTS
Route::get('api/spots','SpotController@get');
Route::post('api/spots/add','SpotController@new');
// END SPOTS

// Route
Route::post('api/iterinary/activity/new',['middleware' => 'cors', 'uses' => 'ActivityController@new']);
Route::post('api/iterinary/activity/get',['middleware' => 'cors', 'uses' => 'ActivityController@get']);
// END ROUTE

//Route::get('api/hotels','')

// --   GEOLOCATION ROUTES -- //
Route::get('geolocationhelper',function(){
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

// -- FOURSQUARE ROUTES -- //
Route::get('testcache',function(){
    $data  = \App\Classes\Rome2rioHelper::call();
    //Cache::add('cebu,manila',$data,20);
    dd($data);
});
//

//  --  SEGMENT ROUTES -- //
Route::get('api/iterinary/segments/all',['middleware' => 'cors', 'uses' => 'SegmentController@getAll']);
Route::get('api/iterinary/segments/show',['middleware' => 'cors', 'uses' => 'SegmentController@showSegment']);
Route::post('api/iterinary/segments/add',['middleware' => 'cors', 'uses' => 'SegmentController@addNew']);
Route::post('api/iterinary/segments/end',['middleware' => 'cors', 'uses' => 'SegmentController@endSegment']);

//  -- END SEGMENT ROUTES -- //

//  -- STOPS ROUTES -- //
Route::get('api/iterinary/stops/get',['middleware' => 'cors', 'uses' => 'IterinaryController@getStops']);
Route::post('api/iterinary/stops/add',['middleware' => 'cors', 'uses' => 'SegmentController@addStop']);
//  -- END STOP ROUTES -- //

//  -- POPULATE TABLES -- //

Route::get('populate/routes',['middleware' => 'cors', 'uses' => 'TestController@populateRoutes']);
Route::get('populate/spots',['middleware' => 'cors', 'uses' => 'TestController@populateSpots']);

//  -- END -- //


//  -- test helpers -- //
Route::get('test/helpers',function(){

App\Classes\FoursquareHelper::testHelper();
App\Classes\Rome2rioHelper::testHelper();
App\Classes\GeolocationHelper::testHelper();
App\Classes\recommenderModule::testHelper();

});
// -- end test helpers -- //
