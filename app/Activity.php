<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Activity extends Model
{
    //

    protected $table = 'activities';

    
    
    public function iterinary(){

    }

    public function interest()
    {
    	return $this->hasMany('App\Interest');
    }

}

