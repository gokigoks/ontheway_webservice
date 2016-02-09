<?php namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * App\Category
 *
 * @property integer $id
 * @property string $category_name
 * @property \Carbon\Carbon $created_at
 * @property \Carbon\Carbon $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Eat[] $eats
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Spot[] $spots
 */
class Category extends Model {

	protected $tables = 'categories';



	public function eats()
	{
		return $this->belongsToMany('App\Eat');
	}


	public function spots()
	{
		return $this->belongsToMany('App\Spot');
	}

}
