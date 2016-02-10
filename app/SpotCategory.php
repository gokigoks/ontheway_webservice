<?php namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class SpotCategory
 * @package App
 */
class SpotCategory extends Model {

    protected $table = 'spot_categories';
	//
    public function spots()
    {
        return $this->belongsToMany('App\Spot');
    }
}
