<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Classes\rome2rioHelper as Helper;
use App\Iterinary;
use App\Route;
use App\Segment;
use App\Stop;
use App\User;
use Input;
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
		$data = Helper::call();
		$airports = [];
		$user_id = Input::get('user_id');
		$contributor = User::find($user_id);

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
			$new_route->price = Helper::getRome2RioPrice($route);
			$new_route->save();
			$iterinary->save();
			$new_route->iterinary()->associate($iterinary);

			$i=1;
			foreach ($route->segments as $segment) {

				$new_segment = new Segment();
				$new_segment->mode = (!isset($segment->subkind)) ? $segment->kind : $segment->subkind;
				$new_segment->sequence = $i;
				$new_segment->origin_name = (!isset($segment->sName))? "" : $segment->sName;
				$new_segment->destination_name = (!isset($segment->tName))? "" : $segment->tName;;
				$new_segment->origin_pos = $segment->sPos;
				$new_segment->destination_pos = $segment->tPos;
				$new_segment->price = Helper::getRome2RioPrice($segment);
				$new_segment->path = ($segment->kind == "flight")? Helper::getFlightPath($airports[$segment->sCode],$airports[$segment->tCode]) : $segment->path;
				$new_segment->distance = $segment->distance;
				$new_segment->duration = $segment->duration;
				


				$new_route->segments()->save($new_segment);
				$i++;
			}		

			unset($i); // unset index for segments sequence
		}
		//dd($data);

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
