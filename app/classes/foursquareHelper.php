<?php

namespace App\Classes;


use Carbon\Carbon;
use App\FoodCategory;
use App\SpotCategory;
use Cache;
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


    public static function browseSearch($query_type = null, $ll = null, $category)
    {
        $url = "https://api.foursquare.com/v2/venues/search?intent=browse";

    }

    /**
     * Call foursquare Api
     * @param type|null $query_type
     * @param type|null $ll
     * @param tpye|null $category
     * @return type
     */
    public static function call($query_type = null, $ll = null, $category = null)
    {
        if($category)
        {
            self::browseSearch($query_type,$ll,$category);
        }

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
                die('Request failed: HTTP status code: ' . $resultStatus.'| url:'.$url.' | '.$ll);
            }
        }
        //dd($result,$ch);
        curl_close($ch);
        return $data;

    }

    /**
     * check for identical request in cache
     * @param $query_type type of query (eg. spot,food)
     * @param type $ll Latitude Longitude
     * @return boolean
     */

    public static function checkForCachedQuery($ll, $query_type)
    {
        $key = md5($ll . "" . $query_type);
        if (Cache::has($key)) {
            return Cache::get($key);
        } else {
            return null;
        }

    }


    public static function cacheRequest($ll, $query_type, $data)
    {
        $key = md5($ll);

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
            $image = $image->prefix . "64" . $image->suffix;
        }
        else
        {
//            $image = Spot::where()
        }

        return $image;
    }

    public static function saveSpotCategories($categories)
    {
        //discarded categories
       // dd($categories);
        $discard_items = ['College & University', 'Professional & Other Places','Residence'];
        $collection = collect([]);
        foreach($categories as $key => $category){
            if(in_array($category->name,$discard_items))
            {
                unset($categories[$key]);
            }
        }

        foreach($categories as $key=>$category)
        {

            if($category->name == "Food")
            {
                echo 'food cat??? '. $category->name.'\n';
                self::saveFoodCategories($category);
            }
            else{

                 foreach($category->categories as $sub_category){
                        $spot_category = new SpotCategory();
                        $spot_category->main_cat = $category->name;
                        $spot_category->main_cat_id = $category->id;
                        $spot_category->sub_cat = $sub_category->name;
                        $spot_category->sub_cat_id = $sub_category->id;
                        $spot_category->icon_url = $category->icon->prefix."64".$category->icon->suffix;
                        $spot_category->save();
                        $collection->push($spot_category);
                        unset($spot_category);
                }

                $key = str_slug($category->name, "-");
                Cache::forever($key,$collection);
                unset($collection);
                $collection = collect([]);
            }

        }

    }

    public static function saveFoodCategories($categories)
    {   $collection = collect([]);
        foreach($categories->categories as $category){
            $food_category = new FoodCategory();
            $food_category->main_cat = $categories->name;
            $food_category->main_cat_id = $categories->id;
            $food_category->sub_cat = $category->name;
            $food_category->sub_cat_id = $category->id;
            $food_category->icon_url = $categories->icon->prefix.'64'.$categories->icon->suffix;
            $collection->push($food_category);
            $food_category->save();
            //var_dump($food_category);
            unset($food_category);
        }

        $key = str_slug($categories->name, "-");
        Cache::forever($key,$collection);
        unset($collection);
    }

    public static function getFoodMainCategory($food_cat){
//        return response()->json('error sa resolve food MAIN cat');
        $category = FoodCategory::where('main_cat_id',$food_cat)
                    ->orWhere('sub_cat_id',$food_cat)
                    ->first();
        if(!$category)
        {
            return 'error';
        }
        return $category->main_cat_id;
    }

    public static function getFoodSubCategory($food_cat)
    {
//        return response()->json('error sa resolve food SUB cat');
        $category = FoodCategory::where('sub_cat_id','=',$food_cat)
                    ->first();

        return (!isset($category->sub_cat_id)) ? null : $category->sub_cat_id;
    }

    public static function resolveFoodCategory($food_data)
    {
        //return response()->json('error sa resolve food cat');
        $food_cat = [
          'sub_cat' => self::getFoodSubCategory($food_data),
            'main_cat' => self::getFoodMainCategory($food_data)
        ];

        if($food_cat['main_cat'] == 'error')
        {
            $food_cat['error_code'] == "403";
            $food_cat['message'] = "main category id not valid";
        }
        return $food_cat;
    }

    public static function getSpotMainCategory($spot_cat){
//        return response()->json('dre error dapite sa MAIN');
        // dd($spot_cat);
        $category = SpotCategory::where('main_cat_id',$spot_cat)
            ->orWhere('sub_cat_id',$spot_cat)
            ->first();
        return $category->main_cat_id;
    }

    public static function getSpotSubCategory($spot_cat)
    {
//        return response()->json('dre error dapita sa SUB');
        $category = SpotCategory::where('sub_cat_id','=',$spot_cat)
            ->first();

        return (!isset($category->sub_cat_id)) ? null : $category->sub_cat_id;
    }

    public static function resolveSpotCategory($food_data)
    {
        $food_cat = [
            'sub_cat' => self::getSpotSubCategory($food_data),
            'main_cat' => self::getSpotMainCategory($food_data)
        ];

        return $food_cat;
    }


}

?>