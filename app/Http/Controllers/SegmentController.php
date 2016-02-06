<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class SegmentController extends Controller {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function getAll()
	{
		
	}

	/**
	 * Show one segment
	 * @param iterinary id
	 * @return type
	 */
	public function showSegment()
	{
		$segment_id = Input::get('segment_id');

		$iterinary = App\Segment::find($segment_id);

		return response()->json($iterinary,200);
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function addNew()
	{
		//
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function endSegment(Request $request)
	{

	}

	/**
	 * Display the specified resource.
	 *
	 * @param  Request  $request
	 * @return Response
	 */
	public function store(Request $request)
	{
		$segment = new Segment;
		$segment->route_id = $request['route_id'];
		$segment->sequence = $request['sequence'];
		$segment->origin_name = $request['origin_name'];
		$segment->destination_name = $request['destination_name'];
		$segment->origin_pos = $request['origin_pos'];
		$segment->destination_pos['destination_pos'];
		$segment->price = $request['price'];
		$segment->path = (property_exists($request, "path")) ? $request['path'] : "";
		$segment->distance = $request['distance'];
		$segment->mode  = $request['mode'];
		$segment->save();
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$data = Segment::all();

		return response()->json(json_encode($data));
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
