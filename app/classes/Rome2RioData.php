    <?php
/**
 * Created by PhpStorm.
 * User: eldringoks
 * Date: 2016-01-11
 * Time: 9:54 PM
 */

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

    public function getData()
    {
        echo $this->data;
    }


}