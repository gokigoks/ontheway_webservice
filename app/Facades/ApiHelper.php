<?php
/**
 * Created by PhpStorm.
 * User: eldringoks
 * Date: 2015-12-13
 * Time: 2:26 AM
 */


namespace App\Facades;
use Illuminate\Support\Facades\Facade;

class ApiHelper extends Facade{
    protected static function getFacadeAccessor() { return 'ApiHelper'; }
}