<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Iterinary;
use App\User;
use Input;
use Carbon\Carbon;
use App\Route;
use App\Classes\UserSessionHandler;
use App\Classes\GeolocationHelper;
use Illuminate\Database\Eloquent\ModelNotFoundException;


use Illuminate\Http\Request;

class IterinaryController extends Controller
{

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newIterinary(Request $request)
    {
        $user = '';
        $token = Input::get('token');
        $withSegment = Input::get('withSegment');
        $error_bag = array();

        if ($token == null) return response()->json('token is empty', 200);

        $user = userSessionHandler::getByToken($token);
//        $user = $user->filter(function($item)
//        {
//            return $item->id = $item->getAttribute('id');
//        })->first();
        $user = $user->first();


        if ($user == null) {
            if(Auth::check())
            {
                $user = Auth::user();
            }
            return response()->json('user not found.', 404);
        }

        $origin = Input::get('origin');
        $destination = Input::get('destination');
        $pax = Input::get('pax');

        $input_bag = [
            'origin' => $origin,
            'destination' => $destination,
            'pax' => $pax,
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

        $iterinary = new Iterinary();
        $iterinary->creator_id = $user->getAttribute('id');
        $iterinary->origin = $origin;
        $iterinary->destination = $destination;
        $iterinary->pax = $pax;
        //dd('dre dapita errr');
        //$iterinary->save();
        //dd('dre dapita error');
        if ($user->iterinaries()->save($iterinary)) {
            //$user->iterinaries()->attach($iterinary->id);
            $route = new Route;
            $route->name = $iterinary->origin . ' to ' . $iterinary->destination;
            $route->save();
            $iterinary->route()->associate($route);
            $iterinary->save();

            return response()->json($iterinary, 200);
        } else {
            return response()->json('error saving', 401);
        }
    }

    /**
     * contributor new iterinary
     * @param $request
     * @route 'plot/iterinary/new'
     * @return Response
     */
    public function newIterinaryTest(Request $request)
    {
        $token = Input::get('token');
        $withSegment = Input::get('withSegment');
        $error_bag = array();

        if ($token == null) return response()->json('token is empty', 200);

        $user = userSessionHandler::user($token);
        dd($user);
        if ($user == null) {
            return response()->json('user not found.', 404);
        }

        $lng = Input::get('lng');
        $lat = Input::get('lat');
        $origin = Input::get('origin');
        $destination = Input::get('destination');
        $origin_pos = $lat . ',' . $lng;
        $pax = Input::get('pax');
        $date_start = Carbon::now();

        $input_bag = [
            'origin' => $origin,
            'destination' => $destination,
            'pax' => $pax,
            'longitude' => $lng,
            'latitude' => $lat,
            'date_start' => $date_start,
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

        $iterinary = new Iterinary();
        $iterinary->creator_id = $user->id;
        $iterinary->origin = $origin;
        $iterinary->destination = $destination;


        if ($user->iterinaries()->save($iterinary)) {
            //$user->iterinaries()->attach($iterinary->id);
            $pivot = $user->iterinaries()->wherePivot('iterinary_id', '=', $iterinary->id)->first();
            $pivot->pivot->date_start = $date_start;
            $pivot->pivot->save();
            $route = new Route;
            $route->name = $iterinary->origin . ' to ' . $iterinary->destination;
            $route->save();
            $iterinary->route()->associate($route);

            if ($withSegment == true) {
                $segment = new Segment;
                $input_bag = [
                    'origin name' => $origin,
                    'origin position' => $destination,
                    'user id' => $user->id,
                    'origin position' => $origin_pos,
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

                $segment->origin_pos = Input::get('origin_pos');
                $segment->sequence = Input::get('sequence');
                $segment->mode = Input::get('mode');
                $segment->origin_name = Input::get('origin_name');

                $route->segments()->save($segment);
            }
            return response()->json('success', 200);
        } else {
            return response()->json('error saving', 401);
        }

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function getPlanned()
    {
        $token = Input::get('token');
        $user_id = Input::get('user_id');

        if (!$token && !$user_id) return response()->json('user id or token must be supplied');

        $user = User::find($user_id);
        $data = $user->planned_iterinaries()->get();
        if ($data->isEmpty()) {
            return response()->json("empty", 404);
        } else {
            return response()->json($data, 200);
        }
    }

    /**
     * Get past iterinaries
     *
     * @return Response
     */
    public function getPast()
    {
        $token = Input::get('token');
        $user_id = Input::get('user_id');

        if (!$token && !$user_id) return response()->json('user id or token must be supplied');

        $user = User::find($user_id);
        if (!$user) {
            $user = UserSessionHandler::getByToken($token);
        }

        $data = $user->past_iterinaries()->get();
        if ($data->isEmpty()) {
            return response()->json('empty', 404);
        } else {
            return response()->json($data, 200);
        }

    }

    /**
     * Get current iterinary
     *
     * @return Response
     */
    public function getCurrent()
    {
        $token = Input::get('token');
        $user_id = Input::get('user_id');

        if (!$token && !$user_id) return response()->json('user id or token must be supplied');
        $user = User::find($user_id);

        if (!$user) {
            $user = UserSessionHandler::getByToken($token);
        }

        $data = $user->current_iterinary()->first();
        if (!$data->count() > 0) {
            return response()->json('empty', 404);
        } else {
            return response()->json($data, 200);
        }
    }

    public function getAll()
    {
        $token = Input::get('token');
        $user_id = Input::get('user_id');

        if (!$token && !$user_id) return response()->json('user id or token must be supplied');
        $user = User::find($user_id);
        if (!$user) {
            $user = UserSessionHandler::getByToken($token);
        }

        $data = $user->iterinaries()->get();

        if ($data->isEmpty()) {
            return response()->json('empty', 404);
        } else {
            return response()->json($data, 200);
        }

    }


    public function startPlannedIterinary(Request $request)
    {
        $request = $request->all();
        $token = $request['token'];
        $iterinary_id = $request['iterinary_id'];
        if (!$token) return response()->json('token must be supplied');
        if (!$iterinary_id) return response()->json('iterinary id must be supplied');

        $user = UserSessionHandler::getByToken($token);
        $iterinary = Iterinary::find($iterinary_id);

        $current = $user->current_iterinary()->first();

        if (!$current) {

            $pivot_fields = ['date_start' => Carbon::now(), 'status' => 'doing'];
            $user->current_iterinary()->save($iterinary);
            $user->current_iterinary()->updateExistingPivot($iterinary->id, $pivot_fields, true);
            return response()->json('saved', 200);

        } else {

            $pivot_fields = ['status' => 'planned'];
            $user->current_iterinary()->updateExistingPivot($iterinary->id, $pivot_fields, true);
            return response()->json('updated', 200);

        }

    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function getPath()
    {
        $route_id = Input::get('route_id');
        if (!$route_id) return response()->json('id?', 400);
        $route = Route::find($route_id);
        if (!$route) return response()->json('route not found', 404);
//        dd($iterinary)
        $segments = $route->segments()->get();

        if ($segments->isEmpty()) return response()->json('error', 400);

        $points = [];

        foreach ($segments as $segment) {
            $points[] = GeolocationHelper::decode($segment->path);
        }
        $center[] = $points[0][0];
        $center[] += $points[0][1];

        $points = GeolocationHelper::flatten($points);
        $path = GeolocationHelper::encode($points);

        $data = ['center' => $center, 'path' => $path];

        return response()->json($data, 200);
    }

    public function getRoute()
    {
        $id = Input::get('id');
        if (!$id) return response()->json('id is required', 400);

        $route = Route::find($id);
        if ($route->count() > 0) {

            return response()->json($route, 200);
        }
        return response()->json('no route found', 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
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
     * @param  $request
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
     * @param $request
     * @route 'plot/iterinary/end'
     * @response json
     * */
    public function endIterinary(Request $request)
    {
        $user_id = Input::get('user_id');
        $iterinary_id = Input::get('iterinary_id');

        dd($user_id, $iterinary_id);
    }

    public function copyIterinary(Request $request)
    {
        $request = $request->all();
        $token = $request['token'];
        $iterinary_id = $request['iterinary_id'];

        $iterinary = Iterinary::find($iterinary_id);
        $user = UserSessionHandler::getByToken($token);

        $pivot_fields = ['date_start' => Carbon::now(), 'status' => 'planned'];
        $user->iterinaries()->attach($iterinary_id, $pivot_fields);
//        $user->iterinaries()->updateExistingPivot($iterinary->id, $pivot_fields, true);

        return response()->json('success', 200);
    }


//end
}
