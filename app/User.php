<?php namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;

class User extends Model implements AuthenticatableContract, CanResetPasswordContract {

	use Authenticatable, CanResetPassword;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */


	protected $fillable = ['name', 'email', 'password','address','age','gender'];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = ['password', 'remember_token'];



	/**
	 * Define DATA in pivot table
	 * 
	 * @return return relationship with pivot table data.
	 */
	public function iterinaries()
	{
		return $this->belongsToMany('App\Iterinary')->withPivot('date_start','status');
	}

	/** 	1 -- TO -- MANY
	 * 
	 * Defines relationship to authored iterinaries
	 * 
	 * @return type
	 */

	public function authored_iterinaries()
	{
		return $this->hasMany('App\Iterinary','creator_id');
	}
	/**
	 * user interest relationships
	 * @return relationship
	 */
	
	public function interests()
	{
		return $this->hasMany('App\Interest');
	}


	/**
	 * Scope for all planned iterinaries of a user
	 * @return type
	 */
	public function planned_iterinaries()
	{
		return $this->iterinaries()->where('status','=','planned');
	}

	/**
	 * Scope for current user iterinary
	 * @return type
	 */
	public function current_iterinary()
	{
		return $this->iterinaries()->doing();
	}

	/**
	 * scope for user's past iterinaries
	 * @return relationship
	 */
	public function past_iterinaries()
	{
		return $this->iterinaries()->where('status','=','done');
	}
		
}
