<?php

namespace App\Http\Controllers;

use App\Schedule;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function index($user_id,Request $request)
	{
		$link = $request->link;
		$connected = $request->social;
		$text = $request->content_text;
		$title = $request->message;
		$img = $request->img;
		$img = explode("/",$img);
		$img = $img[2]."/".$img[3]."/".$img[4];
		$img_upload = $request->img_upload;
		if($img_upload!=null) $image = $img_upload;
		elseif ($img!=null) $image = $img;
		else $image = null;
		$schedule_date = $request->calendar." ".$request->time;
		$utc = strtotime($schedule_date);
//date('m/d/y h:i A',$utc) petqa galu
		//dump( date('m/d/y h:i A',$utc) );
		$schedule_insert = Schedule::create([
			'user_id'      => $user_id,
			'provider'     => $connected,
			'title'        => $title,
			'text'         => $text,
			'img'          => $image,
			'link'         => $link,
			'status'       => 0,
			'schedule_date'=> $schedule_date,
		]);
		if(isset($schedule_insert->id) && $schedule_insert->id!=null){
			$schedule = [
				'provider'=>$schedule_insert->provider,
				'datetime'=>$schedule_date
			];
			return $schedule;
		}else{
			return "Error! Schedule post";
		}

	}
}
