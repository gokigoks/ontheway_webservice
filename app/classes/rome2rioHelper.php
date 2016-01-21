<?php

namespace App\Classes;


/**
 * Class Rome2RioData
 * @package app\classes
 */


class Rome2RioData
{
    protected $data = "rome 2 rio data";
    /**
     * Rome2RioData constructor.
     */
    public function __construct()
    {

    }

    public static function getData()
    {
        echo $this->data;
    }


}
?>