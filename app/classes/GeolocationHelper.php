<?php

namespace App\Classes;


/**
 * Class Rome2RioData
 * @package app\classes
 */


class GeolocationHelper
{
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



}
?>