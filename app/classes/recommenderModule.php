<?php

namespace App\Classes;

use App\User;
use App\Iterinar
use App\Rating;
use Carbon;
/**
 * Class Rome2RioData
 * @package app\classes
 */


class RecommenderModule
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


    /**
     * Call foursquare Api
     * @param type|null $query_type 
     * @param type|null $ll 
     * @return type
     */

    public static function generateRoute($user)
    {
        
    }
}
?>