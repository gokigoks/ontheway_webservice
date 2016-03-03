<?php

namespace App\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class FourSquareData
 * @package app\classes
 */

class FourSquareData extends Facade
{
    protected $data = "rome 2 rio data";
    /**
     * FourSquareData class constructor.
     */
    public function __construct()
    {

    }

    public static function call($query_type = null, $ll = null,$area = null)
    {       
        $area = (!$area) ? $area : 'cebu';
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.foursquare.com/v2/venues/explore?ll='.$ll.'&oauth_token=1MZTZYIARGVDAGDQAHOVESDUR3P4OFZA2ABTIBESMJNNJM0T&v=20160106');

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

    public static function cacheRequest()
    {

    }
    


}


?>