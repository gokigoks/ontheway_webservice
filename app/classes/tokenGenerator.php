<?php
/**
 * Created by PhpStorm.
 * User: eldringoks
 * Date: 2016-02-11
 * Time: 10:30 PM
 */

namespace App\Classes;


class tokenGenerator
{
    /**
     * @var
     */
    public $prefix;

    /**
     * @var
     */
    public $entropy;

    /**
     * @param string $prefix
     * @param bool $entropy
     */
    public function __construct($prefix = '', $entropy = false)
    {
        $this->uuid = uniqid($prefix, $entropy);
    }

    /**
     * Limit the UUID by a number of characters
     *
     * @param $length
     * @param int $start
     * @return $this
     */
    public function limit($length = 15, $start = 0)
    {
        $this->uuid = substr($this->uuid, $start, $length);

        return $this;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->uuid;
    }
}
