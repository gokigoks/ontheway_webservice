<?php

namespace App\Classes;


use Carbon;
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

    public static function getData()
    {
        echo 'data';
    }

    public static function parseLongLat($data)
    {            
            list($long, $lat) = explode(",", $data);
            $array = ['long' => $long,'lat' => $lat];
        return $array;
    }
    

    public static function Decode($string)
    {
        $points = array();
        $index = $i = 0;
        $previous = array(0,0);
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

    public static function Encode($points)
    {
        $points = self::Flatten($points);
        $encodedString = '';
        $index = 0;
        $previous = array(0,0);
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
        if($withName == true){

            foreach ($airports as $airport) {
                $array[$key] = self::parseLongLat($airport->pos);
                $array[$key]['city'] = $airport->name;
                $array[$key]['countryCode'] = $airport->countryCode;
             }
        }
        else{

            foreach ($airports as $key => $airport) {

                $array[$key] = self::parseLongLat($airport->pos);               

            }

        }

        return $array;
    }
}
?>