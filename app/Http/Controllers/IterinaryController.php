<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Iterinary;
use App\User;
use Input;
use App\Route;


use Illuminate\Http\Request;

class IterinaryController extends Controller {

	/**
	 * Display a listing of the resource.
	 * @route 'plot/iterinary/new'
	 * @return Response
	 */
	public function newIterinary(Request $request) 
	{
		$user_id = Input::get('user_id');
		$withSegment = Input::get('withSegment');
		$error_bag = array();


		
    	$user = User::find($user_id);
    	
    	$origin = Input::get('origin');
    	$destination = Input::get('destination');
		$origin_pos = Input::get('origin_pos');
		$pax = Input::get('pax');

    	$input_bag = [
				'origin' => $origin,
				'destination' => $destination,
				'user id' => $user_id,
				'pax' => $pax,
		];

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
		//filter of false or null values
		if(array_filter($error_bag))
		{			
			return response()->json($error_bag,400);
		}
    	
    	
    	$iterinary = new Iterinary();
    	$iterinary->creator_id = $user_id;
    	$iterinary->origin = $origin;
    	$iterinary->destination = $destination;
    	$iterinary->save();
    	

    	if($iterinary->users()->save($user))
    	{	    		
    		$user->iterinaries()->attach($iterinary->id);
    		$route = new Route;
    		$route->save();
    		$iterinary->route()->associate($route);

			if($withSegment == true)
			{
				$segment = new Segment;
				$input_bag = [
						'origin name' => $origin,
						'origin position' => $destination,
						'user id' => $user_id,
						'origin position' => $origin_pos,
				];

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
				//filter of false or null values
				if(array_filter($error_bag))
				{
					return response()->json($error_bag,400);
				}

				$segment->origin_pos = Input::get('origin_pos');
				$segment->sequence = Input::get('sequence');
				$segment->mode = Input::get('mode');
				$segment->origin_name = Input::get('origin_name');


				$route->segments()->save($segment);

			}
    		return response()->json('success',200);	
    	}
    	else
    	{
    		return response()->json('error saving',401);
    	}    	

	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function getPlanned()
	{
		$user_id = Input::get('user_id');
		$user = User::find($user_id);
		$data = $user->planned_iterinaries()->get();
		if($data->isEmpty())
		{
			return response()->json($data,404);
		}
		else
		{
			return response()->json($data,200);
		}
	}

	/**
	 * Get past iterinaries
	 *
	 * @return Response
	 */

	public function getPast()
	{
		$user_id = Input::get('user_id');
		$user = App\User::find($user_id);
		$data = $user->past_iterinaries()->get();
		if($data->isEmpty())
		{
			return response()->json('empty',404);
		}
		else
		{
			return response()->json($data,200);
		}

	}

	/**
	 * Get current iterinary
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function getCurrent($id)
	{
		$user_id = Input::get('user_id');
		$user = App\User::find($user_id);
		$data = $user->current_iterinaries()->get();
		if($data->isEmpty())
		{
			return response()->json('empty',404);
		}
		else
		{
			return response()->json($data,200);
		}		
	}


	public function addSpot()
	{
		//$iterinary_id = 
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$data = Iterinary::all();
		return response()->json(json_encode($data));
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function store(Request $request)
	{	
		$ite = new Iterinary;
		$ite->destination = $request['destination'];
		$ite->origin = $request['origin'];
		$ite->route_id = $request['route_id'];
		$ite->save();
	}

	public function getIterinaries()
	{

	}

	public function showIterinary()
	{

	}

	/**
	 *
	 * @route 'plot/iterinary/end'
	 * @response json
	 * */
	public function endIterinary(Request $request)
	{
		$user_id = Input::get('user_id');
		$iterinary_id = Input::get('iterinary_id');

		dd($user_id,$iterinary_id);
	}
}
