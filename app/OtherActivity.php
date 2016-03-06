<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class OtherActivity extends Model {

	//
    protected $fillable = ['expense','name','review'];

    public function activity(){
        return $this->morphMany('App\Activity','typable');
    }

}
