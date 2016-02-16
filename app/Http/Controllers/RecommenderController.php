<?php namespace App\Http\Controllers;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Classes\Rome2rioHelper as Rome2Rio;
use Illuminate\Http\Request;
class RecommenderController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function get_recommend(Request $request)
    {
        $data = \Input::all();
        /**
         * $origin and $destination pwede ra e static
         * tang tanga lang ang parameter na $request
         */
        $origin = \Input::get('origin');
        $destination = \Input::get('destination');
        $origin = "cebu";
        $destination = "manila";
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
        $airports = [];
        if (isset($data->airports)) {
            foreach ($data->airports as $airport) {
                $airports[$airport->code] = $airport->pos;
            }
        }
        // dd($data);
        // start debug
        $routes = \Rome2RioData::getRoutes($data, 0);
        // dd($routes);
        $segments = \Rome2RioData::getSegments($routes);
        foreach ($segments as $value) {
            if ($value->kind == "flight") {
                $value = Rome2Rio::convertToFlightSegment($value, $data);
                $flightpath = Rome2Rio::getFlightPath($airports[$value->sCode], $airports[$value->tCode]);
                $value->{"path"} = $flightpath;
            }
        }
        // dd($segments);
        // dd($segments);
        //end debug
        return response()->json($segments,200);
        // dd($data,$ch);

        curl_close($ch);
        return response()->json([$request->all(),$data],'200');
    }
    /**
     * get trip recommendatinos
     * @param $segment current segment
     * @param $user model
     * @return Response
     */
    public function getTripRecommendations()
    {

    }
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function getStopRecommendations()
    {

    }
    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store()
    {
        //
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id)
    {
        //
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }
    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id)
    {
        //
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }
}
