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
	protected $object;
    //protected static function getFacadeAccessor() { return 'Rome2RioData'; }

	public function __construct($object){
		$this->object = $object;
	}

    public static function getData(){
    	dd('data?');
    }
    /**
     * get routes function
     * @param type|null $index 
     * @return json object || array
     */
    public function getRoutes($index = null){
    	
    	if($index ! = null)
    	{
    		return $data->routes['index'];
    	}
    	else
    	{
    		return $data->routes;
    	}

    	

    }
}

?>