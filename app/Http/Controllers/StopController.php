	<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class StopController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function addStop()
	{
		$segment_id = Input::get('segment_id');
		$segment = App\segment::find($iterinary_id);

		$stop = new App\Stop;
		$stop->name = Input::get('name');
		$stop->kind = Input::get('kind');
		$stop->city = Input::get('city');
		$stop->pos = Input::get('pos');
		$stop->tips = Input::get('tips');
		$stop->region_code = Input::get('region_code');

		if($segment->stops()->save($stop))
		{
			return response()->json('success',200);
		}
		else
		{
			return response()->json('failed',500);
		}

	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function getStops()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store(Request $request)
	{
		$stop =  new Stop;
		$stop->name = $request['stop'];
		$stop->kind = $request['kind'];
		$stop->city = $request['city'];
		$stop->pos = $request['pos'];
		$stop->tips = $request['tips'];
		$stop->timezone = $request['timezone'];
		$stop->region_code = $request['region_code'];
		$stop->save();
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$data = Stop::all();
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
