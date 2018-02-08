<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Social extends Model
{
    //
	protected $table = "social";
	//protected $primaryKey = "provider";

	/*protected $guarded = false;

	public function posteds()
	{
		return $this->hasOne('App\Posted','provider','provider');
	}*/
}
