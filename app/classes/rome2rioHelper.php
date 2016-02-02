<?php

namespace App\Classes;

use Carbon;
use Cache;
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
    
    /**
     * get routes function
     * @param type|null $index 
     * @return json object || array
     */
    public static function getRoutes($data, $index = null){
        
        
        if(property_exists($data, 'routes') ||$index != null)
        {
            return $data->routes[$index];
        }
        if(property_exists($data, 'routes') || $index )
        {
            return $data->segments;
        }
        else
        {
            //dd($data->routes);
            return $data->routes;
        }
    }

    /**
     * Get Segments Function
     * @param index object | default NULL
     * @return Object
     */
    public static function getSegments($data,$index = null)
    {
        if(property_exists($data, 'segments') || $index != null)
        {   //dd('has segments');
            return $data->segments[$index];
        }
        if(!property_exists($data, 'segments') || $index )
        {
            return $data->segments;
        }
        else
        {   //dd('nosegments');
            return "error.. no segments";
        }
    }

    /**
     * purpose ani nga function kay pra ma kuha ang price
     * most objects in rome2rio has indicative price sub array
     * get native price if existing or get price then multiply by USD value.
     * @return integer
     */
    public static function getRome2RioPrice($data, $index = null)
    {   
        if(property_exists($data, 'indicativePrice'))
        {   
            ///////////
            $price = $data->indicativePrice;    
            return (property_exists($price, 'nativePrice')) ? "datanativePrice" : ($price->price * 42);
            //eturn $data->indicativePrice->nativePrice;
        }
        if(property_exists($data, 'nativePrice'))
        {
            return $data->nativePrice;
        }
        
    }

    public static function call($o = null,$d = null)
    {   
        //default for testing    
        $origin = ($o!=null) ? $o :'cebu' ;
        $destination  = ($d!=null) ? $d :'manila' ;
        //


        if(self::checkForCachedQuery($origin,$destination))
        {   
            $origin = str_replace(" ", "-", $origin);
            $destination = str_replace(" ", "-", $destination);
            $key = $origin.",".$destination;
            $data = Cache::get($key);
            $data->fromCache = true;
            //dd('from cache');
        }
        else
        {

            /**
             * $url = API urls
             * kani ray ilisi earl
             */
            $url = "http://free.rome2rio.com/api/1.2/json/Search?key=nKYL3BZS&oName=".$origin."&dName=".$destination;

            $ch = curl_init($url);

            curl_setopt($ch, CURLOPT_TIMEOUT, 5);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $data = curl_exec($ch);

            $data = json_decode($data);
            $data->fromCache = false;
            //dd('not from cache');
            self::cacheRequest($origin,$destination,$data);
            
            //close
            curl_close($ch);
        }

        return $data;                    

    }

    public static function convertToFlightSegment($route,$segment){

       foreach ($route->stops as $stop) {

           if(property_exists($stop, 'code'))
           {   
               //checks if this stop code matches the origin code
               if($stop->code == $segment->sCode)
               {   //then adds the property to the segment
                   $segment->sPos = $stop->pos;
                   $segment->sName = $stop->name;
               }
               if($stop->code == $segment->tCode)
               {
                   $segment->tPos = $stop->pos;
                   $segment->tName = $stop->name;
               }
           }            
       }
       //new segment
       return $segment;
    }
        
    

    public static function cacheRequest($origin,$destination,$data)
    {   
        $origin = str_replace(" ", "-", $origin);
        $destination = str_replace(" ", "-", $destination);
        $key = $origin.",".$destination;

        if(Cache::has($key)){

        }
        else
        {   
            $expiresAt = Carbon::now()->addHours(2);

            cache::add($key,$data,$expiresAt);
        }

    }

    public static function checkForCachedQuery($origin,$destination)
    {
        $origin = str_replace(" ", "-", $origin);
        $destination = str_replace(" ", "-", $destination);
        $key = $origin.",".$destination;

        if(Cache::has($key))
        {   
            return true;
        }
        else
        {
            return false;
        }
    }


}
?>