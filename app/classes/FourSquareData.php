<?php
/**
 * Created by PhpStorm.
 * User: eldringoks
 * Date: 2016-01-11
 * Time: 9:24 PM
 */

namespace App\Classes;


/**
 * Class FourSquareData
 * @package app\classes
 */

class FourSquareData
{
    protected $api;
    protected $query_type;
    protected $data;

    /**
     * FourSquareData constructor.
     */
    public function __construct()
    {
        $this->data = "four square data initialized";
    }

    public function getData()
    {
        echo $this->data;
    }
}