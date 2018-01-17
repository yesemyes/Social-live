<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    //
	protected $fillable = [
		'user_id',
		'title',
		'text',
		'img',
		'status',
		'timezone',
		'created_at',
		'updated_at'
	];
}
