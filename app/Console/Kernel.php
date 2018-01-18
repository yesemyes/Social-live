<?php
namespace App\Console;
use DB;
use Carbon\Carbon;
use App\Http\Controllers\SocialController;
use App\Oauth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Posted as SchedulePosts;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
class Kernel extends ConsoleKernel
{
	/**
	 * The Artisan commands provided by your application.
	 *
	 * @var array
	 */
	protected $commands = [
		// \App\Console\Commands\Inspire::class,
	];
	/**
	 * Define the application's command schedule.
	 *
	 * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
	 * @return void
	 */
	protected function schedule(Schedule $schedule)
	{
		/*$schedule->command('inspire')
					 ->everyMinute();
		dd($schedule);*/
		$schedule->call(function ()
		{
			$socialClass = new SocialController();
			$SchedulePosts = SchedulePosts::where('status',0)->get();
			$user_id = 0;
			$request = collect();
			foreach($SchedulePosts as $key => $item)
			{
				$utc_now_time = Carbon::now('UTC')->addHour($item->timezone);
				$now_utc = strtotime($utc_now_time);
				$post_utc = strtotime($item->schedule_date);
				if($now_utc>=$post_utc)
				{
					if($user_id != $item->user_id) {
						$user_id = $item->user_id;
						$oAuth = Oauth::where('user_id',$user_id)->get();
					}
					if(isset($oAuth[$key]))
					{
						foreach($oAuth as $k => $v)
						{
							if($v->provider == $item->provider) {
								$soc = $item->provider;
								$request->id = $item->id;
								$request->img_link = url(Storage::url($item->img));
								$request->img_link_ins = Storage::url($item->img);
								$request->link = $item->link;
								$request->message = $item->title;
								$request->content_text = $item->text;
								$request->token_soc = $v->access_token;
								$request->token_soc_sec = $v->access_token_secret;
								$request->username = $v->first_name;
								$request->password = $v->access_token;
								$request->boards_id = $item->boards_id;
								$request->subreddits_id = $item->subreddits_id;
								$socialClass->$soc($request);
							}
						}
					}
				}
			}
		})->everyMinute();
	}
	/**
	 * Register the Closure based commands for the application.
	 *
	 * @return void
	 */
	protected function commands()
	{
		require base_path('routes/console.php');
	}
}