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

    public static function call($url,)
    {

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