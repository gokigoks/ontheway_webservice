<?php

namespace App\Classes;


use Carbon\Carbon;

/**
 * Class Rome2RioData
 * @package app\classes
 */
class GeolocationHelper
{
    protected static $precision = 5;

    protected $data = "geolocation data";

    /**
     * Rome2RioData constructor.
     */
    public function __construct()
    {

    }

    public static function testHelper()
    {
        echo "geolocation helper";
    }

    public static function parseLongLat($data)
    {

        list($lat, $long) = explode(",", $data);
        $array = [$lat, $long];
        return $array;
    }


    public static function Decode($string)
    {
        $points = array();
        $index = $i = 0;
        $previous = array(0, 0);
        while ($i < strlen($string)) {
            $shift = $result = 0x00;
            do {
                $bit = ord(substr($string, $i++)) - 63;
                $result |= ($bit & 0x1f) << $shift;
                $shift += 5;
            } while ($bit >= 0x20);
            $diff = ($result & 1) ? ~($result >> 1) : ($result >> 1);
            $number = $previous[$index % 2] + $diff;
            $previous[$index % 2] = $number;
            $index++;
            $points[] = $number * 1 / pow(10, static::$precision);
        }
        return $points;
    }

    public static function Pair($list)
    {
        $pairs = array();
        if (!is_array($list)) {
            return $pairs;
        }
        do {
            $pairs[] = array(
                array_shift($list),
                array_shift($list)
            );
        } while (!empty($list));
        return $pairs;
    }

    public static function Flatten($array)
    {
        $flatten = array();

        array_walk_recursive(
            $array, // @codeCoverageIgnore
            function ($current) use (&$flatten) {
                $flatten[] = $current;
            }
        );

        return $flatten;
    }


    public static function addPathToPath($first, $second)
    {
        $first_path = self::decode($first);
        $second_path = self::decode($second);

        $final = array_merge($first_path, $second_path);

        return self::encode($final);
    }

    public static function Encode($points)
    {
        $points = self::Flatten($points);
        $encodedString = '';
        $index = 0;
        $previous = array(0, 0);
        foreach ($points as $number) {
            $number = (float)($number);
            $number = (int)round($number * pow(10, static::$precision));
            $diff = $number - $previous[$index % 2];
            $previous[$index % 2] = $number;
            $number = $diff;
            $index++;
            $number = ($number < 0) ? ~($number << 1) : ($number << 1);
            $chunk = '';
            while ($number >= 0x20) {
                $chunk .= chr((0x20 | ($number & 0x1f)) + 63);
                $number >>= 5;
            }
            $chunk .= chr($number + 63);

            $encodedString .= $chunk;
        }
        return $encodedString;
    }

    public static function getAirportLongLat($data, $withName = false)
    {
        $airports = $data->airports;
        $array = array();
        if ($withName == true) {
            $i = 0;
            foreach ($airports as $airport) {
                $array[$i] = self::parseLongLat($airport->pos);
                $array[$i]['city'] = $airport->name;
                $array[$i]['countryCode'] = $airport->countryCode;
                $i++;
            }
        } else {

            foreach ($airports as $key => $airport) {

                $array[$key] = self::parseLongLat($airport->pos);

            }

        }

        return $array;
    }

    /*
     *  find the n closest locations
     *  @param Model $query eloquent model
     *  @param float $lat latitude of the point of interest
     *  @param float $lng longitude of the point of interest
     *  @param float $max_distance distance in miles or km
     *  @param string $units miles or kilometers
     *  @param Array $fiels to return
     *  @return array
     **/
    public static function haversine($query, $lat, $lng, $max_distance = 20, $units = 'kilometers', $fields = false)
    {

        if (empty($lat)) {
            $lat = 0;
        }
        if (empty($lng)) {
            $lng = 0;
        }
        /*
         *  Allow for changing of units of measurement
         */
        switch ($units) {
            case 'miles':
                //radius of the great circle in miles
                $gr_circle_radius = 3959;
                break;
            case 'kilometers':
                //radius of the great circle in kilometers
                $gr_circle_radius = 6371;
                break;
        }
        /*
         *  Support the selection of certain fields
         */
        if (!$fields) {
            $fields = array('users.*', 'users_profile.*', 'users.username as user_name');
        }
        /*
         *  Generate the select field for disctance
         */
        $distance_select = sprintf(
            "
                                    ROUND(( %d * acos( cos( radians(%s) ) " .
            " * cos( radians( lat ) ) " .
            " * cos( radians( lng ) - radians(%s) ) " .
            " + sin( radians(%s) ) * sin( radians( lat ) ) " .
            " ) " .
            ")
                                    , 2 ) " .
            "AS distance
                                    ",
            $gr_circle_radius,
            $lat,
            $lng,
            $lat
        );

        $data = $query->select(DB::raw(implode(',', $fields) . ',' . $distance_select))
            ->having('distance', '<=', $max_distance)
            ->orderBy('distance', 'ASC')
            ->get();

        //echo '<pre>';
        //echo $query->toSQL();
        //echo $distance_select;
        //echo '</pre>';    
        //die();    
        //
        //$queries = DB::getQueryLog();
        //$last_query = end($queries);
        //var_dump($last_query);
        //die();
        return $data;
    }

    public static function sanitizePoints($points)
    {

        foreach ($points as $key => $value) {
            $points[$key] = self::parseLongLat($value);
        }

        return $points;
    }

    /**
     * @param $segment
     * @return int $duration in minutes
     */
    public static function calculateDuration($segment)
    {
        $start = new Carbon($segment->created_at);
        $end = Carbon::now();

        $duration = $end->diffInMinutes($start);

        return $duration;
    }

    public static function calculateDistance($segment)
    {
        $origin = self::parseLongLat($segment->origin_pos);
        $destination = self::parseLongLat($segment->destination_pos);

        return self::distance($origin[0], $origin[1], $destination[0], $destination[1]);
        //sreturn $duration;
    }

    public static function distance($lat1, $lon1, $lat2, $lon2, $unit = "k")
    {

        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        $distance = $miles;
        if ($unit == "K") {
            $distance = $miles * 1.609344;
        } else if ($unit == "N") {
            $distance = $miles * 0.8684;
        } else {

        }

        return round($distance, 0);
    }

    public static function getPlaceName($ll)
    {
        $ll = (!isset($ll)) ? "10.30903,123.8931" : $ll;
        $url = "https://maps.googleapis.com/maps/api/geocode/json?latlng=" . $ll . "&key=AIzaSyDBYczEUp2hpIEhRgm2LbSWHI3qvMo4jQ0";

        $ch = curl_init($url);

        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);

        if (curl_errno($ch)) {
            die("Couldn't send request: " . curl_error($ch));
        } else {

            $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($resultStatus == 200) {

                $data = json_decode($data);
                $data->fromCache = false;
                //dd('not from cache');


            } else {
                // the request did not complete as expected. common errors are 4xx
                // (not found, bad request, etc.) and 5xx (usually concerning
                // errors/exceptions in the remote script execution)

                die('Request failed: HTTP status code: ' . $resultStatus);
            }
        }


    }
}

?>