<?php

namespace App\Classes;


use Carbon;
use Input;
use App\Spot;

/**
 * Class Rome2RioData
 * @package app\classes
 */
class FoursquareHelper
{
    // Foursquare picture format
    protected $pic_url = "https://ss3.4sqi.net/img/categories_v2/parks_outdoors/beach_32.png";

    protected $data = "foursquare data";

    /**
     * Rome2RioData constructor.
     */
    public function __construct()
    {

    }

    public static function testHelper()
    {
        echo "foursquare helper";
    }


    /**
     * Call foursquare Api
     * @param type|null $query_type
     * @param type|null $ll
     * @return type
     */

    public static function call($query_type = null, $ll = null)
    {

        $ll = ($ll != null) ? $ll : "10.211121,123.2019";
        $query_type = (!$query_type) ? 'food' : $query_type;
        $ch = curl_init();
        $url = 'https://api.foursquare.com/v2/venues/search?ll=' . $ll . '&query=' . $query_type . '&oauth_token=1MZTZYIARGVDAGDQAHOVESDUR3P4OFZA2ABTIBESMJNNJM0T&v=20160106';
        curl_setopt($ch, CURLOPT_URL, $url);

        //https://api.foursquare.com/v2/venues/search?ll=10.3156990,123.8854370&oauth_token=1MZTZYIARGVDAGDQAHOVESDUR3P4OFZA2ABTIBESMJNNJM0T&v=20160106&query=food

        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);


        $data = curl_exec($ch);

        if (curl_errno($ch)) {
            die("Couldn't send request: " . curl_error($ch));
        } else {

            $resultStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if ($resultStatus == 200) {

                $data = json_decode($data);
                $data->fromCache = false;
                $data->url = $url;
                $data->query = $query_type;
                //dd('not from cache');
                //self::cacheRequest($area,$data);


            } else {
                // the request did not complete as expected. common errors are 4xx
                // (not found, bad request, etc.) and 5xx (usually concerning
                // errors/exceptions in the remote script execution)

                die('Request failed: HTTP status code: ' . $resultStatus);
            }
        }
        //dd($result,$ch);
        curl_close($ch);
        return $data;

    }

    /**
     * check for identical request in cache
     * @param type $ll Latitude Longitude
     * @return boolean
     */

    public static function checkForCachedQuery($ll, $query_type)
    {
        $key = $ll . "" . $query_type;
        if (Cache::has($key)) {
            return Cache::get($key);
        } else {
            return null;
        }

    }


    public static function cacheRequest($ll, $query_type, $data)
    {


        if (Cache::has($key)) {

        } else {
            $expiresAt = Carbon::now()->addHours(2);

            cache::add($key, $data, $expiresAt);
        }

    }

    public static function getImage($spot)
    {
        $image = (!isset($spot->categories[0]->icon)) ? null : $spot->categories[0]->icon;

        if ($image != null) {
            $image = $image->prefix . "32" . $image->suffix;
        }

        return $image;
    }
}

?>