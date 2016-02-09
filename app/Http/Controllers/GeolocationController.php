<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Classes\GeolocationHelper as GeoHelper;
use Illuminate\Http\Request;


class GeolocationController extends Controller
{

    /**
     * Display a listing of the resource.
     * @param $request
     * @return Response
     */
    public function encode(Request $request)
    {
        $points = $request['points'];

        $array = GeoHelper::sanitizePoints($points);

        $path = GeoHelper::Encode($array);

        return response()->json($path, 200);
    }

    /**
     * Add points to an existing long lat
     * @param @request
     * @return Response
     */
    public function addPointsToPath(Request $request)
    {

        $new_points = $request['points'];
        $array = GeoHelper::sanitizePoints($new_points);

        //dd($array);
        $old_path = $request['path'];


        $decoded_path = GeoHelper::decode($old_path);

        $points = array_merge($decoded_path, $array);

        return GeoHelper::encode($points);

    }

    /**
     * Show the form for creating a new resource.
     * @param $request
     * @return Response
     */
    public function decode(Request $request)
    {
        $path = $request['path'];
        $points = GeoHelper::decode($path);
        return response()->json($points, 200);
    }


    /**
     * Display the specified resource.
     *
     * @param  $request
     * @return Response
     */
    public function addPathToPath(Request $request)
    {
        $first = $request['first_path'];
        $second = $request['second_path'];

        return GeoHelper::addPathToPath($first, $second);
    }


}
