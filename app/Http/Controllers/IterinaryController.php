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
use App\Events\IterinaryWasCopied;
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


        if ($user == null) {
            if (Auth::check()) {
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
            $pivot_fields = [
                'status' => 'doing',
                'date_start' => Carbon::now()->toTimeString()
            ];
            $user->iterinaries()->updateExistingPivot($iterinary->id, $pivot_fields, true);

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

        if (!$token) return response()->json('user id or token must be supplied');
        $user = UserSessionHandler::user($token);

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
    {   //TODO

        $iterinary_id = Input::get('iterinary_id');
        $iterinary = Iterinary::find($iterinary_id);
        $route = $iterinary->route;


        if (!$route) return response()
            ->json(['err' => 'route not found'], 404);
//        dd($iterinary)
        $segments = $route->segments()->get();

        if ($segments->isEmpty()) return response()
            ->json(['err' => 'no segments',
                    'center_lat' => ''], 400);
            $activities = $iterinary->activities()->with('typable')->get();
        $points = [];

        foreach ($segments as $segment) {
            $points[] = GeolocationHelper::decode($segment->path);
        }
        $center[] = $points[0][0];
        $center[] += $points[0][1];

        $points = GeolocationHelper::flatten($points);
        $path = GeolocationHelper::encode($points);

        $data = ['center' => $center, 'path' => $path,'activities' => $activities];

        return response()->json($data, 200);
    }

    public function getRoute()
    {
        $id = Input::get('id');
        if (!$id) return response()->json('id is required', 400);

        $iterinary = Iterinary::find($id);
        $route = $iterinary->route;
        $activities = $iterinary->activities()->with('typable')->get();
        $data = [
            'iterinary' => $iterinary,
            'route' => $route,
            'activities' => $activities
        ];
        if ($route->count() > 0) {

            return response()->json($data, 200);
        }
        return response()->json('no route found', 404);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  $request
     * @return Response
     */
    public function show(Request $request)
    {
        $iterinary_id = $request->input('iterinary_id');
        $iterinary = Iterinary::find($iterinary_id);
        return response()->json($iterinary, 200);
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
     * @return json
     * */
    public function endIterinary(Request $request)
    {
        //TODO
        // update pivot status column to done.
        // calculate routes columns
        $token = Input::get('token');
        $iterinary_id = Input::get('iterinary_id');

        if (!$token || !$iterinary_id) {
            return response()->json('kuwang input', 400);
        }

        $user = UserSessionHandler::user($token);

        $current_iterinary = $user->iterinaries()->find($iterinary_id);

        $route = $current_iterinary->route;
        if (!$route) {
            $current_iterinary->delete(); // e delete nalang
            return response()->json('walay route');
        }

        UserSessionHandler::endIterinary($current_iterinary);

        $pivot = [
            'status' => 'done',
        ];
        $user->iterinaries()
            ->updateExistingPivot($iterinary_id, $pivot, true);
        //dd($user_id, $iterinary_id);
        return response()->json('success', 200);
    }

    public function copyIterinary(Request $request)
    {
        $request = $request->all();
        $token = $request['token'];
        $iterinary_id = $request['iterinary_id'];

        $user = UserSessionHandler::getByToken($token);

        $pivot_fields = ['date_start' => Carbon::now(), 'status' => 'planned'];
        $user->iterinaries()->attach($iterinary_id, $pivot_fields);
//        $user->iterinaries()->updateExistingPivot($iterinary->id, $pivot_fields, true);

        event(new App\Events\IterinaryWasCopied($iterinary_id));
//        \Event::fire(new(App\EventsIterinaryWasCopied($iterinary_id)));

        return response()->json('success', 200);
    }

    public function deleteIterinary(Request $request)
    {
        $request = $request->all();
        $token = $request['token'];
        $iterinary_id = $request['iterinary_id'];
        $error_bag = [];
        $input_bag = [
            'token' => $token,
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

        $user = UserSessionHandler::getByToken($token);

        $user->iterinaries()->detach($iterinary_id);

        return response()->json('delete successful', 200);
    }

    public function startIterinary()
    {
        //
    }

    public function setIterinaryStartDate(Request $request)
    {
        $request = $request->all();
        $token = $request['token'];
        $iterinary_id = $request['iterinary_id'];
        $date = $request['start_date'];
        $error_bag = [];

        $input_bag = [
            'token' => $token,
            'iterinary id' => $iterinary_id,
            'start date' => $date,
        ];

        $i = 0;
        foreach ($input_bag as $key => $value) {
            $value = trim($value);

            if (empty($value)) {
                $error_bag[$i] = "$key is empty";
                $i++;
            } else {
                //
            }
        }
        //filter of false or null values
        if (array_filter($error_bag)) {
            return response()->json($error_bag, 400);
        }

        $start_date = Carbon::parse($date);

        return UserSessionHandler::setIterinaryStartDate($token, $iterinary_id, $start_date);

    }

    //TODO
    // check if date is 00 00 00 in mobile
//end
}
