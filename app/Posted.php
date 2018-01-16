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
		'status',
		'schedule_date',
		'timezone',
		'created_at',
		'updated_at'
	];
}
