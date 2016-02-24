<?php namespace App;

use Illuminate\Database\Eloquent\Model;

class UserSession extends Model {

	//
    protected $table = 'user_sessions';

    protected $fillable = ['payload_id','token'];



}
