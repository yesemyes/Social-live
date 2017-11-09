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
		'created_at',
		'updated_at'
	];
}
