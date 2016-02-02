<?php

namespace App\Classes;


use Carbon;
/**
 * Class Rome2RioData
 * @package app\classes
 */


class FoursquareHelper
{
    protected $data = "foursquare data";
    /**
     * Rome2RioData constructor.
     */
    public function __construct()
    {
        
    }

    public static function getData()
    {
        echo 'hello';
    }

    public static function call($query_type = null, $ii = null)
    {       
        $query_type = (!$query_type) ? $query_type : 'food';
        $ch = curl_init();
        $url = 'https://api.foursquare.com/v2/venues/search?ll='.$ii.'&query='.$query_type.'&oauth_token=1MZTZYIARGVDAGDQAHOVESDUR3P4OFZA2ABTIBESMJNNJM0T&v=20160106';
        curl_setopt($ch, CURLOPT_URL, $url);
        
        //https://api.foursquare.com/v2/venues/search?ll=10.3156990,123.8854370&oauth_token=1MZTZYIARGVDAGDQAHOVESDUR3P4OFZA2ABTIBESMJNNJM0T&v=20160106&query=food

        curl_setopt($ch, CURLOPT_TIMEOUT,    5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


        $contents = curl_exec ($ch);
        $result = json_decode($contents);
        $result->url = $url;
        $result->query = $query_type;
        //dd($result,$ch);
        curl_close($ch);
        return $result;
        
    }


}
?>