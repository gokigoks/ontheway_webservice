<?php namespace App\Http\Controllers;

use App\Activity;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Classes\UserSessionHandler;
use App\Iterinary;
use Illuminate\Http\Request;
use App\Segment;
use App\Hotel;
use Carbon\Carbon;
use App\Stop;
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

            return UserSessionHandler::addSegment($token, $origin_name, $lng, $lat, $mode);
        }
        if ($type == 'food') {
            $food = $request['food'];
            $transpo = $request['transpo'];

            $input = [
                'food' => $food,
                'transpo' => $transpo
            ];

//            return response()->json($input);
//            $input_bag = [
//                'food object is ' => $food
//            ];
//            return response()->json('err dre dit');
//
//            $i = 0;
//            foreach ($input_bag as $key => $value) {
//                $value = trim($value);
//
//                if (empty($value)) {
//                    $error_bag[$i] = "$key empty";
//                    $i++;
//                } else {
//                    //
//                }
//            }
//            //filter of false or null values
//            if (array_filter($error_bag)) {
//                return response()->json($error_bag, 400);
//            }
//            return response()->json($food);
            return UserSessionHandler::addFood($token, $food, $transpo);
//            return response()->json(UserSessionHandler::addFood($token, $food, $transpo),403);
        }
        if ($type == "spots" || $type == 'spot') {

            $spot = $request['spot'];
            $transpo = $request['transpo'];


            return UserSessionHandler::addSpot($token, $spot, $transpo);
//            return response()->json(UserSessionHandler::addSpot($token, $spot,$transpo),403);
        }
        if ($type == 'hotel') {
            //todo
            $hotel = $request['hote'];
            $transpo = $request['transpo'];

            dd($request);
            return UserSessionHandler::addHotel($token, $hotel, $transpo);
        }
        if ($type == 'stop') {
            $stop = $request['stop'];
            $transpo = $request['transpo'];
            return UserSessionHandler::addStop($token, $stop, $transpo);
        }
        if ($type == 'others') {
            $others_data = $request['others'];
            $transpo = $request['transpo'];
            return UserSessionHandler::addOtherActivity($token, $others_data, $transpo);
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
        $iterinary_id = Input::get('iterinary');
//        return response()->json($iterinary_id);
        $token = Input::get('token');
        if (isset($iterinary_id) || $iterinary_id) {

            $iterinary = Iterinary::find($iterinary_id);
            if (!isset($iterinary) || $iterinary == null) {

                $message = [
                    'err' => '404',
                    'message' => ' iterinary not found'
                ];
                return response()->json($message);
            }
            $activities = $iterinary->activities()->with('typable')->get();
            return response()->json($activities, 200);
        } else {

            $iterinary = UserSessionHandler::getUserCurrentIterinary($token);
            if ($iterinary['err'] == 'err') return response()->json($iterinary, 403);
            if (!isset($iterinary) || $iterinary == null) {

                $message = [
                    'err' => '403',
                    'message' => 'no current iterinary'
                ];
                return response()->json($message);
            }

            $activities = $iterinary->activities()->with('typable')->get();
            return response()->json($activities, 200);
        }

    }

    public function checkIn(Request $request)
    {
        $hotel_data = $request->input('hotel');
        $transpo_data = $request->input('transpo');
        $token = $request->input('token');
//        $inputs = $request->all();
//        return response()->json($inputs);
        $current_iterinary = UserSessionHandler::getUserCurrentIterinary($token);

        $response = UserSessionHandler::resolveNewSegmentFromActivity($token, $transpo_data, $hotel_data);

        $day = UserSessionHandler::getDiffInDays($token, $current_iterinary->id);
        $activity = new Activity();
        $activity->start_time = Carbon::now()->toTimeString();
        $activity->iterinary_id = $current_iterinary->id;
        $activity->day = $day;

        $hotel = new Hotel();
        $hotel->place_name = $hotel_data['place_name'];
        $hotel->lng = $hotel_data['lng'];
        $hotel->lat = $hotel_data['lat'];
        if (isset($hotel['foursquare_id'])) {
            $hotel->foursquare_id = $hotel_data['foursquare_id'];
        }
        $hotel->save();
        $hotel->activity()->save($activity);

        return response()->json($hotel);
    }

    public function checkOutFromHotel(Request $request)
    {
        $token = $request->input('token');
        $current_iterinary = UserSessionHandler::getUserCurrentIterinary($token);
        $hotel = $current_iterinary->activities()->hotel()->first();
        $hotel = Hotel::find($hotel->typable_id);

        $request = $request->all();

        $hotel->price = $request['price'];
        $hotel->tips = $request['review'];
        $now = Carbon::now();
        $day = $now->diffInDays($hotel->created_at);

        $hotel->days_stayed = ($day == 0) ? 1 : $day;

        $hotel->update();
        $hotel->touch();

        UserSessionHandler::updateIterinary($token);

        return response()->json($hotel);
    }

    public function addStop(Request $request)
    {
        $stop_data = $request->input('stop');
        $transpo_data = $request->input('transpo');
        $token = $request->input('token');
        $stop = new Stop;
        $stop->place_name = $stop_data['place_name'];
        $stop->details = $stop_data['stop_details'];
        $stop->price = $stop_data['price'];
        $stop->lat = $stop_data['lat'];
        $stop->lng = $stop_data['lng'];

        UserSessionHandler::resolveNewSegmentFromActivity($token, $transpo_data, $stop);
        $current_iterinary = UserSessionHandler::getUserCurrentIterinary($token);
//        return $current_iterinary;s
        if ($current_iterinary['err'] == 'err') {
            return response()->json($current_iterinary, 403);
        }

        $day = UserSessionHandler::getDiffInDays($token, $current_iterinary->id);
        $activity = new Activity();
        $activity->start_time = Carbon::now()->toTimeString();
        $activity->iterinary_id = $current_iterinary->id;
        $activity->day = $day;

        $stop->save();
        $stop->activity()->save($activity);

        return response()->json($stop);
    }
}
