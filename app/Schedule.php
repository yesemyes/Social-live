<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
class Schedule extends Model
{
	protected $table = "schedule_posts";
	protected $fillable = [
		'user_id',
		'provider',
		'title',
		'text',
		'img',
		'link',
		'status',
		'schedule_date',
		'created_at',
		'updated_at'
	];
}