<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Zizaco\Entrust\Traits\EntrustUserTrait;

use DB;

class User extends Authenticatable
{
    //use Notifiable;
	use EntrustUserTrait; // add this trait to your user model

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	//protected $table = 'users';

	protected $guarded = false;

	 protected $fillable = [
        'name', 'email', 'password','user_url',
    ];


    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function posteds()
    {
    	return $this->hasMany('App\Posted');
    }

	public function posts()
	{
		return $this->hasMany('App\Post');
	}

	public function connectedAccounts()
	{
		return $this->hasMany('App\Oauth');
	}
}
