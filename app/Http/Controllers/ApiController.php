<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Classes\Rome2rioHelper as Rome2rio;
use Input;
use File;
use Response;
use Cache;
use Illuminate\Http\Request;

class ApiController extends Controller
{


    /**
     * get user function
     *
     * @return json response
     */

    public function get_users()
    {
        $users = App\User::all();
        return response()->json(json_encode($users), 200);
    }

    public function foursquare()
    {
        $ll = Input::get('ll');

        $data = App\Clsses\foursquareHelper::getData();

        dd($data);
    }

    /**
     * Get Hotels
     *
     * @return hotel collection from database
     */

    public function get_hotels()
    {
        return response()->json('success', 200);
    }

    public function login()
    {
        return response()->json('success', 200);
    }

    public function get_foursquare()
    {
        return view('api.foursquare');
    }

    public function post_foursquare(Request $request)
    {

        $area = $request['area'];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.foursquare.com/v2/venues/explore?near=' . $area . '&oauth_token=1MZTZYIARGVDAGDQAHOVESDUR3P4OFZA2ABTIBESMJNNJM0T&v=20160106');

        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


        $contents = curl_exec($ch);
        $result = json_decode($contents);

        dd($result, $ch);

        return response()->json($contents, 200);
        curl_close($ch);
    }

    /**
     * Display rome2rio api call response
     *
     * @return Response
     */
    public function get_rome2rio(Request $request)
    {

        return view('api.rome2rio');
    }


    public function post_rome2rio(Request $request)
    {
        /**
         * $origin and $destination pwede ra e static
         * tang tanga lang ang parameter na $request
         */
        $origin = $request['origin'];
        $destination = $request['destination'];
        /**
         * $url = API url
         * kani ray ilisi earl
         */

        //$url = "http://free.rome2rio.com/api/1.2/json/Search?key=nKYL3BZS&oName=".$origin."&dName=".$destination;
//
//        $ch = curl_init($url);
//
//        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
//        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = Rome2rio::call($origin, $destination);

        //$data = json_decode($data)
        dd($data);
        return response()->json($data, 200);

        curl_close($ch);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function testAjax()
    {
        $input = \Request::input('token');
        $data = ['connection' => ' success', 'some data' => $input];
        $data = json_encode($data);
        return response()->json($data, 200);
    }

    /**
     * test helper file with rome2rio dataset
     * @param Request $request
     * @return json response status 200
     */
    public function test_rome2rio_helper(Request $request)
    {
        /**
         * $origin and $destination pwede ra e static
         * tang tanga lang ang parameter na $request
         */
        $origin = $request['origin'];
        $destination = $request['destination'];
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
        // start debug
        $routes = App\Classes\rome2rioHelper::getRoutes($data, 1);
        $segments = App\Classes\rome2rioHelper::getSegments($routes);
        dd($segments);
        //end debug
        return response()->json($data, 200);
        dd($data, $ch);

        curl_close($ch);
    }


    /**
     * handle image wildcards
     * @param $image_path
     * @return image response
     */
    public function imageHandler($image_path)
    {

            if( File::exists(public_path().'/images/'.$image_path) ){

                $filetype = File::type( public_path().'/images/'.$image_path );

                $response = Response::make( File::get( public_path().'/images/'.$image_path ) , 200 );

                $response->header('Content-Type', $filetype);

                return $response;
            }
            else{
                return response()->json('image not found',404);
            }

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

}
