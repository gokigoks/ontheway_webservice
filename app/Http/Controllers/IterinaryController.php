<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Iterinary;

use Illuminate\Http\Request;

class IterinaryController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function new(Request $request) 
	{
		$credentials = array(
        'email' => Input::get('email'), 
        'password' => Input::get('password'),            
    	);

    	$user_id = Input::get('user_id');
    	$user = App\User::find($user_id);
    	$withRoute = Input::get('withRoute');
    	$origin = Input::get('origin');
    	$destination = Input::get('destination');
    	$iterinary = new Iterinary();
    	$iterinary->creator_id = $user_id;
    	$iterinary->origin = $origin;
    	$iterinary->destination = $destination;
    	$iterinary->save();
    	

    	if($iterinary->users()->save($user))
    	{	
    		if($withRoute == true)
    		{
    			$route = new App\Route;
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
		$user = App\User::find($user_id);
		$data = $user->planned_iterinaries()->get();
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

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function addSpot()
	{
		$iterinary_id = 
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

}
