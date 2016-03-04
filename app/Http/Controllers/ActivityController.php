<?php namespace App\Http\Controllers;

use App\Activity;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Classes\UserSessionHandler;
use App\Iterinary;
use Illuminate\Http\Request;
use App\Segment;
use Input;

class ActivityController extends Controller
{
    /**
     * @param $request
     * @route 'api/activity/add'
     * @return Response
     */
    public function addActivity(Request $request)
    {
        $error_bag = [];

        $request = $request->all();
        $type = $request['type'];

        $token = $request['token'];

        if ($type == "transport" || $type == 'transpo') {

            $iterinary_id = $request['iterinary_id'];
            $origin_name = $request['place_name'];
            $lng = $request['lng'];
            $lat = $request['lat'];
            $mode = $request['mode'];
            $price = $request['price'];
            $input_bag = [
                'origin name ' => $origin_name,
                'longitude ' => $lng,
                'latitude ' => $lat,
                'mode' => $mode,
                'iterinary id' => $iterinary_id,
            ];

            $i = 0;
            foreach ($input_bag as $key => $value) {
                $value = trim($value);

                if (empty($value)) {
                    $error_bag[$i] = "$key empty";
                    $i++;
                } else {
                    //
                }
            }
            //filter of false or null values
            if (array_filter($error_bag)) {
                return response()->json($error_bag, 400);
            }

            return UserSessionHandler::addSegment($token, $iterinary_id, $origin_name, $lng, $lat, $mode);
        }
        if ($type == 'food') {

            $food = $request['food'];
            $segment = $request['segment'];
            $input_bag = [
                'food object is ' => $food
            ];

            $i = 0;
            foreach ($input_bag as $key => $value) {
                $value = trim($value);

                if (empty($value)) {
                    $error_bag[$i] = "$key empty";
                    $i++;
                } else {
                    //
                }
            }
            //filter of false or null values
            if (array_filter($error_bag)) {
                return response()->json($error_bag, 400);
            }

            return UserSessionHandler::addFood($token, $food,$segment);
        }
        if ($type == "spots" || $type == 'spot') {

            $spot = $request['spot'];

            $segment = $request['segment'];

            $input_bag = [
                'spot' => $spot,
            ];

            $i = 0;
            foreach ($input_bag as $key => $value) {
                $value = trim($value);

                if (empty($value)) {
                    $error_bag[$i] = "$key empty";
                    $i++;
                } else {
                    //
                }
            }
            //filter of false or null values
            if (array_filter($error_bag)) {
                return response()->json($error_bag, 400);
            }

            return UserSessionHandler::addSpot($token, $spot, $segment);

        }
        if($type == 'hotel')
        {
            //todo
        }
        else {

            return response()->json('type field is required', 200);
        }


    }

    /**
     * end the activity
     * @param $request
     * @route 'api/activity/endactivity'
     * @return json
     */
    public function endActivity(Request $request)
    {
        $request = $request->all();
        $token = $request['token'];
        $type = $request['type'];
        if (!$type) return response()->json('error', 400);
        if ($type == 'transpo' || $type == 'transport') {
            $iterinary_id = $request['iterinary_id'];
            $destination_name = $request['destination_name'];
            $lng = $request['lng'];
            $lat = $request['lat'];
            $price = $request['price'];
            return UserSessionHandler::endSegment($token, $iterinary_id, $destination_name, $lat, $lng, $price);
        }

        if ($type == 'spot') {
            $iterinary_id = $request['iterinary_id'];
            $price = $request['price'];
            $tips = $request['tips'];
            return UserSessionHandler::endSpotActivity($token, $iterinary_id, $price, $tips);
        }
        if ($type == 'eat') {
            $iterinary_id = $request['iterinary_id'];
            $price = $request['price'];
            $tips = $request['tips'];
            return UserSessionHandler::endFoodActivity($token, $iterinary_id, $price, $tips);
        }

    }

    /**
     *
     * @param $request
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

    public function spotCheckInAutocomplete()
    {
        $word = Input::get('key_word');

    }

    public function spotCategoryAutocomplete()
    {
        //TODO
    }

    public function foodCategoryAutocomplete()
    {
        //TODO
    }

    public function getAll()
    {
        //add a compiled polyline of all activities
        $iterinary_id = Input::get('iterinary_id');
        $iterinary = Iterinary::find($iterinary_id);
        $activities = $iterinary->activities()->with('typable')->get();
        return response()->json($activities,200);

    }
}
