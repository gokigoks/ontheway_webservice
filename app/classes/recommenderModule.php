<?php

namespace App\Classes;

use App\User;
use App\Iterinary;
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

    public static function testHelper()
    {
        echo "recommender helper";
    }


    /**
     * Call foursquare Api
     * @param $user Model
     * @param $iterinary Model
     * @return type
     */

    public static function generateRoute($user, $iterinary)
    {

    }
}

?>