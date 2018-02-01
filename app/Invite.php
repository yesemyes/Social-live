<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Invite extends Model
{
	protected $fillable = [
		'email', 'token','user_id',
	];

	/*public function getEmail()
	{
		return $this->where('email','toghramajyan@inbox.ru');
	}*/
}
