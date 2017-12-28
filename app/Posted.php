<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Posted extends Model
{
    //
	protected $table = "posted";
	protected $fillable = [
		'user_id',
		'provider',
		'title',
		'text',
		'img',
		'link',
		'created_at',
		'updated_at'
	];
}
