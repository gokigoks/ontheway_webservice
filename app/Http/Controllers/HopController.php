<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class HopController extends Controller {

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
	public function create()
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
		$hop = new Hop;
		$hop->segment_id = $request['segment_id'];
		$hop->source_area_code = $request['source_area_code'];
		$hop->url = $request['url'];
		$hop->target_area_code = $request['target_area_code'];
		$hop->source_terminal = $request['source_terminal'];
		$hop->target_terminal = $request['target_terminal'];
		$hop->sTime = $request['sTime'];
		$hop->tTime = $request['tTime'];
		$hop->flight_no = $request['flight_no'];
		$hop->airline_code = $request['airline_code'];
		$hop->duration = $request['duration'];
		$hop->save();
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$data = Hop::all();
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
