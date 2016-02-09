<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

class ActivityController extends Controller
{

    /**
     * Display a listing of the resource.
     * @route 'api/activity/add'
     * @return Response
     */
    public function addActivity(Request $request)
    {
        $type = $request['type'];

        if ($type == "transport") {
            $origin_name = $request['origin_name'];
            $origin_pos = $request['origin_pos'];
            $mode = $request['mode'];

        }
        if ($type == "food") {
            $name = $request['food'];
            $lng = $request['lng'];
            $lat = $request['lat'];
            $price = $request['price'];
        }
        if ($type == "spot") {
            $name = $request['spot'];
            $lng = $request['lng'];
            $lat = $request['lat'];
            $segment_id = $request['segment_id'];

        } else {
            return response()->json('type field is required', 200);
        }


    }

    /**s
     * end the activity
     *
     * @route 'api/activity/endactivity'
     * @reponse json
     */
    public function endActivity(Request $request)
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request)
    {
        $activity = new Activity;
        $activity->day_id = $request['day_id'];
        $activity->start_time = $request['start_time'];
        $activity->end_time = $request['typable_type'];
        $activity->typable_id = $request['typable_id'];
        $activity->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function show($id)
    {
        $data = Activity::all();
        return response()->json(json_encode($data));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int $id
     * @return Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @return Response
     */
    public function update($id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return Response
     */
    public function destroy($id)
    {
        //
    }

}
