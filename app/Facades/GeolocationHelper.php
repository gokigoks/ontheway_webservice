<?php

namespace app\Facades;
use Illuminate\Support\Facades\Facade;

/**
 * Class GeolocationHelper
 * @package app\classes
 */

class GeolocationHelper extends Facade
{
    protected $data = "rome 2 rio data";
    protected static function getFacadeAccessor() { return 'GeolocationHelper'; }

    public static function call($area == null)
    {       
        $area = (!$area) ? $area : 'cebu';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.foursquare.com/v2/venues/explore?near='.$area.'&oauth_token=1MZTZYIARGVDAGDQAHOVESDUR3P4OFZA2ABTIBESMJNNJM0T&v=20160106');

        curl_setopt($ch, CURLOPT_TIMEOUT,    5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


        $contents = curl_exec ($ch);
        $result = json_decode($contents);

        //dd($result,$ch);
        curl_close($ch);
        return $result;
        
    }

    public static function getData()
    {
        return 'hello';
    }

    public static function getNearest($area)
    {   

    }

    public static function getWithLongLat($pos)
    {

    }   

    


}

?>