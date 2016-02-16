<?php
namespace App\Classes;

use Carbon\Carbon;
use Cache;
use App\Classes\GeolocationHelper as Geohelper;

/**
 * Class Rome2RioData
 * @package app\classes
 */
class Rome2rioHelper
{
    protected $data = "rome 2 rio data";

    /**
     * Rome2RioData constructor.
     */

    public static function testHelper()
    {
        echo "rome2riohelper helpe&\n";
    }

    /**
     * get routes function
     * @param $data
     * @return json object || array
     */
    public static function getRoutes($data, $index = null)
    {


        if (property_exists($data, 'routes') || $index != null) {
            return $data->routes[$index];
        }
        if (property_exists($data, 'routes') || $index) {
            return $data->segments;
        } else {
            //dd($data->routes);
            return $data->routes;
        }
    }

    /**
     * Get Segments Function
     * @param index object | default NULL
     * @return Object
     */
    public static function getSegments($data, $index = null)
    {
        if (property_exists($data, 'segments') || $index != null) {   //dd('has segments');
            return $data->segments[$index];
        }
        if (!property_exists($data, 'segments') || $index) {
            return $data->segments;
        } else {   //dd('nosegments');
            return "error.. no segments";
        }
    }

    /**
     * purpose ani nga function kay pra ma kuha ang price
     * most objects in rome2rio has indicative price sub array
     * get native price if existing or get price then multiply by USD value.
     * @param $data
     * @return integer
     */
    public static function getRome2RioPrice($data)
    {
        if (isset($data->indicativePrice)) {
            ///////////
            $price = $data->indicativePrice;
            return (!isset($price->nativePrice)) ? ($price->price * 42) : $price->nativePrice;
            //eturn $data->indicativePrice->nativePrice;
        }

    }

    public static function call($o = null, $d = null)
    {
        //default for testing    
        $origin = ($o != null) ? $o : 'cebu';
        $destination = ($d != null) ? $d : 'manila';
        //

        if (self::checkForCachedQuery($origin, $destination)) {
            $origin = str_replace(" ", "-", $origin);
            $destination = str_replace(" ", "-", $destination);
            $key = $origin . "," . $destination;
            $data = Cache::get($key);
            $data->fromCache = true;
            //dd('from cache');
        } else {

            /**
             * $url = API urls
             * kani ray ilisi earl
             */
            $url = "http://free.rome2rio.com/api/1.2/json/Search?key=nKYL3BZS&oName=" . $origin . "&dName=" . $destination;
            $url = $new = str_replace(' ', '%20', $url);
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
                    self::cacheRequest($origin, $destination, $data);


                } else {
                    // the request did not complete as expected. common errors are 4xx
                    // (not found, bad request, etc.) and 5xx (usually concerning
                    // errors/exceptions in the remote script execution)

                    die('Request failed: HTTP status code: ' . $resultStatus);
                }
            }


        }

        return $data;

    }


    public static function convertToFlightSegment($segment, $data)
    {

        $segment = self::getOrigDest($segment, $data->airports);

        return $segment;
    }

    public static function getOrigDest($segment, $airports)
    {
        foreach ($airports as $airport) {

            if ($airport->code == $segment->sCode) {
                $segment->sName = $airport->name;
                $segment->sPos = $airport->pos;
            }
            if ($airport->code == $segment->tCode) {
                $segment->tName = $airport->name;
                $segment->tPos = $airport->pos;
            }
        }

        return $segment;
    }


    public static function cacheRequest($origin, $destination, $data)
    {
        $origin = str_replace(" ", "-", $origin);
        $destination = str_replace(" ", "-", $destination);
        $key = $origin . "," . $destination;

        if (Cache::has($key)) {

        } else {
            $expiresAt = Carbon::now()->addHours(2);

            cache::add($key, $data, $expiresAt);
        }

    }

    public static function checkForCachedQuery($origin, $destination)
    {
        $origin = str_replace(" ", "-", $origin);
        $destination = str_replace(" ", "-", $destination);
        $key = $origin . "," . $destination;

        if (Cache::has($key)) {
            return true;
        } else {
            return false;
        }
    }

    public static function getFlightPath($origin, $destination)
    {
        $array = [];

        $array[0] = Geohelper::parseLongLat($origin);
        $array[1] = Geohelper::parseLongLat($destination);

        $path = Geohelper::encode($array);

        return $path;
    }


}

?>