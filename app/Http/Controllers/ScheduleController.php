<?php

namespace App\Http\Controllers;

use App\Posted;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ScheduleController extends Controller{
	public function index( $user_id, Request $request ) {
		$timezone   = $request->timezone;
		$updated_at = Carbon::now( 'UTC' )->addHour( $timezone );
		$link       = $request->link;
		$connected  = $request->social;
		$text       = $request->content_text;
		$title      = $request->message;
		if ( isset( $request->img ) && $request->img != "" ) {
			$img        = $request->img;
			$img        = explode( "/", $img );
			$img        = $img[2] . "/" . $img[3] . "/" . $img[4];
			$img_upload = $request->img_upload;
			if ( $img_upload != null ) {
				$image = $img_upload;
			} elseif ( $img != null ) {
				$image = $img;
			} else {
				$image = null;
			}
		} else {
			$image = null;
		}
		if ( isset( $request->boards_id ) && $request->boards_id != "" ) {
			$boards_id = $request->boards_id;
		} else {
			$boards_id = null;
		}
		if ( isset( $request->subreddits_id ) && $request->subreddits_id != "" ) {
			$subreddits_id = $request->subreddits_id;
		} else {
			$subreddits_id = null;
		}
		$schedule_date = $request->calendar . " " . $request->time;

		$schedule_insert = Posted::create( [
			'user_id'       => $user_id,
			'provider'      => $connected,
			'title'         => $title,
			'text'          => $text,
			'img'           => $image,
			'link'          => $link,
			'status'        => 0,
			'boards_id'     => $boards_id,
			'subreddits_id' => $subreddits_id,
			'schedule_date' => $schedule_date,
			'timezone'      => $timezone,
			'created_at'    => $updated_at,
			'updated_at'    => $updated_at,
		] );

		if ( isset( $schedule_insert->id ) && $schedule_insert->id != null ) {
			$schedule = [
				'provider' => $schedule_insert->provider,
				'datetime' => $schedule_date
			];

			return $schedule;
		} else {
			return "Error! Schedule post";
		}
	}
}