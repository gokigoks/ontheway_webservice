<?php
/**
 * Created by PhpStorm.
 * User: eldringoks
 * Date: 2016-01-11
 * Time: 9:51 PM
 */
namespace app\Facades;
use Illuminate\Support\Facades\Facade;
class Rome2RioData extends Facade
{
    protected $data;
    protected static function getFacadeAccessor() { return 'Rome2RioData'; }
    public static function getData(){
        dd('data?');
    }

    /**
     * get routes function
     * @param type|null $index
     * @return json object || array
     */
    public static function getRoutes($data, $index = null){


        if(property_exists($data, 'routes') && $index >= 0)
        {
            return $data->routes[$index];
        }
        else if(property_exists($data, 'routes') && $index )
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
        if(isset($data->segments) && $index != null)
        {	//dd('has segments');
            return $data->segments[$index];
        }
        if(isset($data->segments) && $index == null)
        {
            return $data->segments;
        }
        else
        {	//dd('nosegments');
            // dd($data->segments,$index);
            // dd($data);
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
            return (property_exists($price, 'nativePrice')) ? "datanativePrice" : ($price->currency == "USD" ? $price->price * 42 : $price);
            //eturn $data->indicativePrice->nativePrice;
        }
        if(property_exists($data, 'nativePrice'))
        {
            return $data->nativePrice;
        }

    }
    public static function call($o = null,$d = null)
    {
        $origin = ($o!=null) ? $o :'cebu' ;
        $destination  = ($d!=null) ? $d :'manila' ;
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
        if(curl_errno($ch))
        {
            die("Couldn't send request: " . curl_error($ch));
        }
        else {
            // check the HTTP status code of the request
            $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($resultStatus == 200) {

            } else {
                // the request did not complete as expected. common errors are 4xx
                // (not found, bad request, etc.) and 5xx (usually concerning
                // errors/exceptions in the remote script execution)
                die('Request failed: HTTP status code: ' . $resultStatus);
            }
        }
        $data = json_decode($data);
        dd('dre ko gikanl');
        //close
        curl_close($ch);

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


    public function getStops()
    {
    }
    public function save($type){
    }
}
?>