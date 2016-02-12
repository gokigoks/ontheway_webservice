<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Classes\Rome2rioHelper as Rome2Rio;
use App\Classes\FoursquareHelper as Foursquare;
use Carbon\Carbon;
use App\Iterinary;
use App\Route;
use App\Segment;
use App\Stop;
use App\Spot;
use App\User;
use Input;
use Cache;
use Illuminate\Http\Request;

class TestController extends Controller {

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
		'origin' => Input::get('origin'),
		'destination' => Input::get('destination'),
		'pax' => Input::get('pax')
		];

        $airports = [];
        $user_id = Input::get('user_id');
        $contributor = User::find($user_id);

        $now = Carbon::now();

        if($contributor == null)
        {
            return response()->json('user not found.',404);
        }

        $data = Rome2Rio::call($inputs['origin'],$inputs['destination']);

        if(isset($data->airports)){
			foreach ($data->airports as $airport) {
				$airports[$airport->code] = $airport->pos;
			}
		}

		foreach($data->routes as $route)
		{
			$iterinary = new Iterinary();
			$iterinary->origin = $data->places[0]->name;
			$iterinary->destination = $data->places[1]->name;
			$iterinary->creator_id = $contributor->id;


			$new_route = new Route();
			$new_route->name = $route->name;
			$new_route->distance = $route->distance;
			$new_route->duration = $route->duration;
			$new_route->price = Rome2Rio::getRome2RioPrice($route);
			$new_route->save();

			$iterinary->route()->associate($new_route);
            $iterinary->save();
            if(!$contributor->iterinaries()->attach($iterinary->id,['date_start' => $now])) return response()->json('ur fucked',400);
            $i=1;
			foreach ($route->segments as $segment) {

				$new_segment = new Segment();

				if($segment->kind == "flight")
				{
					$segment = Rome2Rio::convertToFlightSegment($segment,$data);
				}

				$new_segment->mode = (!isset($segment->subkind)) ? $segment->kind : $segment->subkind;
				$new_segment->sequence = $i;
				$new_segment->origin_name = (!isset($segment->sName))? "" : $segment->sName;
				$new_segment->destination_name = (!isset($segment->tName))? "" : $segment->tName;;
				$new_segment->origin_pos = $segment->sPos;
				$new_segment->destination_pos = $segment->tPos;
				$new_segment->price = Rome2Rio::getRome2RioPrice($segment);
				$new_segment->path = ($segment->kind == "flight")? Rome2Rio::getFlightPath($airports[$segment->sCode],$airports[$segment->tCode]) : $segment->path;
				$new_segment->distance = $segment->distance;
				$new_segment->duration = $segment->duration;
				
				$new_route->segments()->save($new_segment);

				// if(isset($segment->stops))
				// {	
				// 	foreach ($segment->stops as $stop) {

				// 		$new_stop = new Stop();
				// 		$new_stop->name = $stop->name;
				// 		$new_stop->pos = $stop->pos;
				// 		$new_stop->kind = $stop->kind;
				// 		$new_stop->city = "";						
				// 		$new_stop->tips = "";
				// 		$new_stop->timezone = (!isset($stop->timeZone))? "" : $stop->timeZone;
						
				// 		$segment->
				// 	}
						 

				// }

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
        if($query_type==null) $query_type = "beach";
		$data = Foursquare::call($query_type,$ll);

		$spots = (!isset($data->response->venues)) ? null : $data->response->venues;
		if($spots)
        {
        	foreach ($spots as $spot) {

        		$new_spot = new Spot();
        		$new_spot->place_name = $spot->name;
        		$new_spot->pic_url = Foursquare::getImage($spot);
        		$new_spot->lat = $spot->location->lat;
        		$new_spot->lng = $spot->location->lng;
        		$new_spot->price = 50 * rand(1,4);
                $new_spot->save();

        	}
        }else{
            return response()->json('your spots query was empty.. try again fo!',400);
        }

		return response()->json('success',200);
	}

	
	public function populateEats()
	{
        $ll = Input::get('ll');
        $query_type = Input::get('query');
        $data = Foursquare::call($query_type,$ll);

        $spots = (!isset($data->response->venues)) ? null : $data->response->venues;
        if($spots)
        {
            foreach ($spots as $spot) {
                $new_spot = new Eat();
                $new_spot->place_name = "name";
                $new_spot->pic_url = Foursquare::getImage($spot);
                $new_spot->lat = $spot->location->lat;
                $new_spot->lng = $spot->location->long;
                $new_spot->price = 125 * rand(1,4);
                $new_spot->save();
            }
        }else{
            return response()->json('your food query was empty, fo!',400);
        }

    }

    /**
     *
     * @return mixed
     */
	public function populateCategories()
	{
        $refresh = Input::get('refresh');
        if($refresh == true)
        {
            Cache::forget('categories');
        }
        if(Cache::has('categories'))
        {
            $data = Cache::get('categories');

            Foursquare::saveSpotCategories($data->response->categories);
        }
        else {

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
                    Cache::add('categories',$data,$expiry);
                    dd(Foursquare::saveSpotCategories($data->response->categories));

                } else {

                    die('Request failed: HTTP status code: ' . $resultStatus);
                }
            }
        }

       // dd(json_decode($data));
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
