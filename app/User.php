<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

use DB;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	//protected $table = 'users';

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

	public function connectedAccounts()
	{
		return $this->hasMany('App\Oauth');
	}

	/*static function byOauth($id, $provider)
	{

		$user = DB::table('oauth')
		          ->join('users', 'oauth.user_id', '=', 'users.id')
		          ->select('users.*')
		          ->where('provider', $provider)
		          ->where('provider_user_id', $id)
		          ->orderBy('id', 'ASC')
		          ->first();

		$user = $user ? self::find($user->id) : false;

		return $user;
	}*/
}
