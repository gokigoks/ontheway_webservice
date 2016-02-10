<?php namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Class FoodCategory
 * @package App
 */
class FoodCategory extends Model
{
    protected $table = 'food_categories';
    //
    public function eats()
    {
        return $this->belongsToMany('App\Eat');
    }
}
