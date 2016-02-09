<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Input;
use App\Spot;
use User;
use Iterinary;
use Illuminate\Http\Request;

class SpotController extends Controller {

	/**
	 * Display a listing of the resource.
	 * 
	 * @request GET
	 * @route api/spots
	 * @return Response
	 */
	public function get()
	{	
		$input_bag = []; // Array for inputs
		$error_bag = []; // Array for errors

		$user_id = Input::get('user_id');
		$iterinary_id = Input::get('iterinary_id');
		$query = Input::get('query');
		$longitude = Input::get('longitude');
		$latitude = Input::get('latitude');
		
		$input_bag = ['user id' => $user_id, 'iterinary id' => $iterinary_id, 'longitude' =>  $longitude, 'latitude' => $latitude];

		$i=0;
		foreach ($input_bag as $key => $value) {
		    $value = trim($value);

		    if (empty($value))
		    {    $error_bag[$i] = "$key empty"; 
		    		$i++;
		    }
		    else{
		        
		    }
		}

		//longitude=123.89434773306299&latitude=10.308967644834725
		//filter of false or null values
		if(array_filter($error_bag))
		{			
			return response()->json($error_bag,400);
		}
		$longlat = $latitude.",".$longitude;
		$data = App\Classes\foursquareHelper::call($query,$longlat);

		dd($data);
		$user = App\User::find($user_id);
		$iterinary = App\Iterinary::find($iterinary_id);
		

	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function newSpot()
	{
		$user_id = Input::get('user_id');
		$iterinary_id = Input::get('iterinary_id');
		$longitude = Input::get('longitude');
		$latitude = Input::get('latitude');
		$place_name = Input::get('place_name');
        $city = Input::get('city');
		$tips = Input::get('tips');
		$price =Input::get('price');
		$type = Input::get('type');
		

		$user = App\User::find($user_id);
		$iterinary = App\Iterinary::find($iterinary_id);


	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		$spot = new Spot;
		$spot->place_name = $request['place_name'];
		$spot->pos = $request['pos'];
		$spot->price = $request['price'];
		$spot->tips = $request['tips'];
		$spot->save();
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$data = Spot::all();
		return response()->json(json_encode($data));
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
