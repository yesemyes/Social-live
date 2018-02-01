<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Posted extends Model
{
	protected $guarded = false;
    //
	protected $table = "posted";
	//protected $primaryKey = "provider";
	protected $fillable = [
		'user_id',
		'provider',
		'title',
		'text',
		'img',
		'link',
		'status',
		'boards_id',
		'subreddits_id',
		'schedule_date',
		'timezone',
		'created_at',
		'updated_at'
	];

	public function socialIcon()
	{
		return $this->hasOne('App\Social','provider','provider');
	}

	public function user()
	{
		return $this->belongsTo('App\User');
	}
}
