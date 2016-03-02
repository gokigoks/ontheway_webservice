<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Classes\Rome2rioHelper as Rome2Rio;
use App\Iterinary;
use App\Route;
use App\Segment;
use App\Contribution;
use App\WeightedAverage;

use App\Events\IterinaryRateWasAdded;
use App\Events\ActivityRateWasAdded;
use App\Classes\userSessionHandler;
use App\Rating;

use Illuminate\Http\Request;

class RecommenderController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function get_recommend($origin="cebu", $destination="paris")
    {
        // $data = \Input::all();

        /**
         * $origin and $destination pwede ra e static
         * tang tanga lang ang parameter na $request
         */
        // $origin = \Input::get('origin');
        // $destination = \Input::get('destination');

        // $origin = "cebu";
        // $destination = "manila";

        $origin = urlencode($origin);
        $destination = urlencode($destination);

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
        // dd($data);

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
        $price = 0;
        foreach ($segments as &$segment) {
            $segment->mode = $segment->kind;
            unset($segment->kind);
            $segment->origin_name = $segment->sName;
            unset($segment->sName);
            $segment->destination_name = $segment->tName;
            unset($segment->tName);
            $segment->origin_pos = $segment->sPos;
            unset($segment->sPos);
            $segment->destination_pos = $segment->tPos;
            unset($segment->tPos);
            $segment->{'price'} = Rome2Rio::getRome2RioPrice($segment);
            $price += Rome2Rio::getRome2RioPrice($segment);
        }

        
        curl_close($ch);
        return $segments;

        // dd($segments);

        // dd($segments);
        //end debug

        // dd($data,$ch);



        // return response()->json([$request->all(),$data],'200');
    }


    public function getRecommendation(Request $request){
        $origin = $request->origin;
        $destination = $request->destination;
        $userbudget = $request->budget;

        // dd($origin,$destination);

        // $origin = "cebu city";
        // $destination = "manila";

        //get closest budget
        $budget = $this->getBudgetRecommendation($userbudget, $origin, $destination);

        if ($budget == 0) {
            // dd("in");
            $segments = $this->get_recommend($origin, $destination);
            // dd($segments);
            return response()->json($segments);
        } else {
            //get iterinaries that falls under the budget
            $iterinary_choices = Iterinary::whereRaw("price <= ?", [$budget])->where("origin", $origin)->where("destination", $destination)->lists("id");
            // dd($iterinary_choices);
            //get the iterinary_id that falls under the budget and is the best in terms of rating
            $suggested_iterinary = WeightedAverage::whereIn("ratingable_id", $iterinary_choices)->where("ratingable_type", "Iterinary")->max("average");
            $suggested_iterinary = WeightedAverage::where("average", $suggested_iterinary)->where("ratingable_type", "Iterinary")->lists("ratingable_id");

            //refers to points if there are iterinaries with the same rate
            // dd($suggested_iterinary);
            if(count($suggested_iterinary)>1){
                $suggested_iterinary = $this->getSuggestedIterinary($suggested_iterinary);

            }
            // dd($suggested_iterinary);
            $route_id = Iterinary::where("id", $suggested_iterinary)->lists("route_id");

            $segments = Segment::where("route_id", $route_id)->get();
            return response()->json($segments);
            // dd($segments);
            // dd($route_id);
            // dd($iterinary_choices);
        }
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function getBudgetRecommendation($budget, $origin, $destination)
    {

        $prices = Iterinary::where("origin", $origin)->where("destination", $destination)->get();
        // $prices = Iterinary::whereRaw("origin = ? and destination = ?", [$origin, $destination])->get();
        $allprice = [];
        // dd($prices);

        if (count($prices)==0) {
            // dd("in");
            return 0;
        } else {

            foreach ($prices as $price) {
                array_push($allprice, $price->price);
            }
            // $budget = 2700;
            $distance = abs($allprice[0]-$budget);
            // dd($distance);
            $idx = 0;
            $distanceHasReplaced = false;
            for ($i=1; $i < count($allprice); $i++) {
                $cdistance = abs($allprice[$i]-$budget);
                // dd($cdistance);
                if($cdistance<$distance && $allprice[$i]<=$budget){
                    $idx = $i;
                    $distance = $cdistance;
                    $distanceHasReplaced = true;
                }
            }

            $closest = $allprice[$idx];

            // $id = Iterinary::where("price", $closest)->lists("id");

            // dd($id[0]);
            // dd($closest);
            // dd($allprice);
            return $closest;
        }

    }

    //uy
    // public function getIterinariesByPlaces($origin, $destination){
    // 	$origin = "cebu city";
    // 	$destination = "manila";

    // 	$keys = [];

    // 	$iterinaries = Iterinary::whereRaw("origin = ? and destination = ?",[$origin,$destination])->get();
    // 	foreach ($iterinaries as $ite) {
    // 		array_push($keys, $ite->id);
    // 	}

    // 	return $keys;
    // }

    public function getSuggestedIterinary($ids){
        // $iterinary_ids = $this->getIterinariesByPlaces($origin, $destination);
        //vars
        $points = [];
        $suggested = [];

        $contributions = Contribution::whereIn("iterinary_id", $ids)->get();
        // dd($contributions);
        foreach ($contributions as $value) {
            array_push($points, $value->points);
        }

        $max = max($points);
        // dd($max);

        foreach ($contributions as $value) {
            if ($value->points == $max) {
                //get the specific column that has the max value of points
                $suggested = $value;
                break;
            }
        }

        return $suggested->iterinary_id;

        // dd($suggested->iterinary_id);
    }

    public function addrating(Request $request){

        $token = $request->token;

        if($token == null) return response()->json('token is empty',200);

        $user = userSessionHandler::user($token);

        if($user == null)
        {
            return response()->json('user not found.',404);
        }

        $rating = new Rating;

        $rating->user_id 		  = $user->id;
        $rating->value 			  = $request->value;
        $rating->ratingable_id	  = $request->ratingable_id;
        $rating->ratingable_type  = $request->ratingable_type;
        $rating->save();

        if ($request->ratingable_type == "Iterinary") {
            \Event::fire(new IterinaryRateWasAdded($request->ratingable_id));
        } else {
            \Event::fire(new ActivityRateWasAdded($request->ratingable_id));
        }
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