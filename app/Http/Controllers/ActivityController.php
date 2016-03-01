<?php namespace App\Http\Controllers;

use App\Activity;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Classes\UserSessionHandler;
use Illuminate\Http\Request;
use App\Segment;

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
        if ($type == "transport" || $type == 'transpo' ) {

            $iterinary_id = $request['iterinary_id'];
            $origin_name = $request['place_name'];
            $lng = $request['lng'];
            $lat = $request['lat'];
            $mode = $request['mode'];
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
        if ($type=='food') {

            $place_name = $request['place_name'];
            $lng = $request['lng'];
            $lat = $request['lat'];
            
            $iterinary_id = $request['iterinary_id'];

            $category = $request['category'];
            $pic_url = "http://php-usjrproject.rhcloud.com/api/img/food.png";
            //$token = $request['token'];

            $input_bag = [
                'place name' => $place_name,
                'longitude ' => $lng,
                'latitude ' => $lat,
                'food category' => $category,
                'iterinary id' => $iterinary_id
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

            return UserSessionHandler::addFood($token, $place_name, $lng, $lat, $category, $iterinary_id);
        }
        if ($type == "spots" || $type== 'spot') {

            $spot_name = $request['place_name'];
            $lng = $request['lng'];
            $lat = $request['lat'];
            $category = $request['category'];
            $iterinary_id = $request['iterinary_id'];
            $pic_url = "http://php-usjrproject.rhcloud.com/api/img/beach.png";

            $input_bag = [
                'spot name' => $spot_name,
                'category' => $category,
                'longitude ' => $lng,
                'latitude ' => $lat,
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

            return UserSessionHandler::addSpot($token, $spot_name, $category, $lat, $lng, $iterinary_id);

        } else {

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

}
