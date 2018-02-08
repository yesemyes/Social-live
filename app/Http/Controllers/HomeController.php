<?php

namespace App\Http\Controllers;

use App\Http\Controllers\SocialController;
use App\Http\Controllers\ScheduleController;
use App\User;
use App\Invite;
use App\Social;
use App\Oauth;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App;
use Hash;
use App\Post;
use App\Posted;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Mail;

class HomeController extends Controller{

	protected $userAuth;
	protected $userID;

	public function __construct()
	{
		$this->middleware('auth');
		$this->middleware(function ($request, $next) {
			$this->userAuth = Auth::user();
			if(!is_null($this->userAuth) && $this->userAuth->hasRole('guest')) {
				$ownerUserID = Invite::select('user_id')->where('email',$this->userAuth->email)->first();
				$this->userID = $ownerUserID->user_id;
			} elseif (!is_null($this->userAuth) && $this->userAuth->hasRole('owner')) {
				$this->userID = $this->userAuth->id;
			}
			return $next($request);
		});

	}

	public function index() {
		/*$posts = $this->userAuth->posted()->select( "social.icon", "posted.*" )
		             ->leftJoin('social','social.provider','=','posted.provider')
		             ->orderBy( 'posted.id', 'desc' )
		             ->get();*/
		$posts = Posted::select( "social.icon", "posted.*" )
						->leftJoin('social','social.provider','=','posted.provider')
		               ->where('user_id',$this->userID)
		               ->orderBy('posted.id','desc')
		               ->get();
		if(count($posts)>0) {
			return view( 'home', [ 'posts' => $posts, 'user' => $this->userAuth ] );
		} else {
			if ($this->userAuth->hasRole('owner')) {
				return redirect( '/create-post' );
			} elseif($this->userAuth->hasRole('guest')) {
				return redirect( '/posts' );
			}
		}
	}

	public function createPost() {
		if($this->userAuth->hasRole('owner')) {
			return view( 'createPost' );
		} elseif($this->userAuth->hasRole('guest')) {
			return redirect( '/posts' );
		}

	}

	public function createPostAction( Request $request ) {
		if ( isset( $request->timezone ) && $request->timezone != null ) {
			$updated_at = Carbon::now( 'UTC' )->addHour( $request->timezone );
			$timezone   = $request->timezone;
		} else {
			$updated_at = Carbon::now();
			$timezone   = null;
		}
		$status = 0;
		if ( isset( $request->publish ) ) {
			$status = 1;
		} elseif ( isset( $request->draft ) ) {
			$status = 0;
		}
		$user    = Auth::user();
		$title   = $request->postTitle;
		$content = $request->postContent;
		$image   = $request->image;
		if ( ! empty( $title ) ) {
			if ( $image != null ) {
				$filename = 'app/' . $image->store( $user['id'] );
			} else {
				$filename = null;
			}
			Post::create( [
				'user_id'    => $user['id'],
				'title'      => $title,
				'text'       => $content,
				'img'        => $filename,
				'status'     => $status,
				'timezone'   => $timezone,
				'created_at' => $updated_at,
				'updated_at' => $updated_at,
			] );
			Session::flash( 'message_success', 'Success! your post created' );

			return redirect( '/posts' );
		} else {
			Session::flash( 'message_error', 'Warning! your post not created' );

			return redirect( '/posts' );
		}
	}

	public function managePosts() {
		$userConnectedAccountsCount = Oauth::select( 'oauth.user_id' )
		                                   ->join( 'users', 'users.id', '=', 'oauth.user_id' )
			                               ->where( 'oauth.user_id', $this->userID )
		                                   ->count();
		$posts = Post::where('user_id',$this->userID)->orderBy('id','desc')->get();
		if ( !is_null($posts) ) {
			return view( 'posts', [
				'userConnectedAccountsCount' => $userConnectedAccountsCount,
				'posts'                      => $posts,
				'user'                       => $this->userAuth
			] );
		} else {
			return redirect( '/create-post' );
		}
	}

	public function editPostAction( $id, Request $request ) {
		if ( isset( $request->timezone ) && $request->timezone != null ) {
			$updated_at = Carbon::now( 'UTC' )->addHour( $request->timezone );
			$timezone   = $request->timezone;
		} else {
			$updated_at = Carbon::now();
			$timezone   = null;
		}
		if ( isset( $request->publish ) ) {
			$status = 1;
		} elseif ( isset( $request->draft ) ) {
			$status = 0;
		} else {
			$status = null;
		}
		$user        = Auth::user();
		$title       = $request->postTitle;
		$content     = $request->postContent;
		$image       = $request->image;
		$default_img = $request->default_img;
		if ( ! empty( $title ) ) {
			if ( $image != null ) {
				$filename = 'app/' . $image->store( $user['id'] );
			} else {
				$filename = $default_img;
			}
			if ( isset( $request->posted ) && $request->posted == 1 ) {
				$post = Posted::where( 'id', $id )
				              ->update( [
					              'title'         => $title,
					              'text'          => $content,
					              'img'           => $filename,
					              'schedule_date' => $request->calendar,
					              'updated_at'    => $updated_at,
				              ] );
			} else {
				if ( $status == 1 || $status == 0 ) {
					$post = Post::where( 'id', $id )
					            ->update( [
						            'title'      => $title,
						            'text'       => $content,
						            'img'        => $filename,
						            'status'     => $status,
						            'timezone'   => $timezone,
						            'created_at' => $updated_at,
						            'updated_at' => $updated_at,
					            ] );
				}
			}
			if ( $post == 1 ) {
				if ( isset( $request->postImgOldUrl ) ) {
					File::delete( storage_path( $request->postImgOldUrl ) );
				}
				if ( isset( $request->schedule ) ) {
					Session::flash( 'message_success', 'Success! your schedule post updated' );

					return redirect()->back();
				}
				Session::flash( 'message_success', 'Success! your post updated' );

				return redirect()->back();
			} else {
				Session::flash( 'message_error', 'Warning! your post not updated' );

				return redirect()->back();
			}
		} else {
			Session::flash( 'message_error', 'Warning! your post not updated' );

			return redirect()->back();
		}
	}

	public function publishPost( $postID, $posted = null ) {
		$socials = Social::get();
		if ($posted != null && $posted == "posted") {
			$post = Posted::where('user_id', $this->userID)->where('id', $postID)->first();
		} else {
			$post = Post::where('user_id', $this->userID)->where('id', $postID)->where('status', 1)->first();
		}
		if ($post == null) {
			Session::flash( 'message_error', 'Warning! your post not published' );
			return redirect( '/edit-post/' . $postID );
		}
		$userConnectedAccounts = Oauth::select( 'oauth.*' )
		                              ->leftJoin( 'users', 'users.id', '=', 'oauth.user_id' )
		                              //->where( 'oauth.user_name', $user->name )
		                              ->where( 'oauth.user_id', $this->userID )
		                              ->get()->keyBy( 'social_id' );
		$userConnectedAccountsCount = count( $userConnectedAccounts );
		$userAccounts               = array();
		$socialClass                = new SocialController();
		$subreddits                 = "";
		$boards                     = "";
		foreach ( $socials as $key => $item ) {
			if ( isset( $userConnectedAccounts[ $item->id ] ) ) {
				$userAccounts[ $key ] = [
					'provider'            => $item->provider,
					'userId'              => $userConnectedAccounts[ $item->id ]->id,
					'provUserId'          => $userConnectedAccounts[ $item->id ]->provider_user_id,
					'icon'                => $item['icon'],
					'access_token'        => $userConnectedAccounts[ $item->id ]->access_token,
					'access_token_secret' => $userConnectedAccounts[ $item->id ]->access_token_secret,
					'first_name'          => $userConnectedAccounts[ $item->id ]->first_name,
					'last_name'           => $userConnectedAccounts[ $item->id ]->last_name,
				];
				if ( isset( $userConnectedAccounts[ $item->id ]->access_token ) && $userConnectedAccounts[ $item->id ]->access_token != "" ) {
					$token = $userConnectedAccounts[ $item->id ]->access_token;
				} else {
					$token = null;
				}
				if ( isset( $item->provider ) && $item->provider == "reddit" ) {
					$get_subreddits = $socialClass->get_subreddits_web( $token );
					if ( isset( $get_subreddits->message ) && $get_subreddits->message == "Unauthorized" ) {
						$subreddits = null;
					} else {
						$subreddits = $socialClass->get_subreddits_web( $token );
					}
				}
				if ( isset( $item->provider ) && $item->provider == "pinterest" ) {
					$get_boards = $socialClass->get_boards_web( $token );
					if ( $get_boards == [] ) {
						$boards = null;
					} else {
						$boards = $socialClass->get_boards_web( $token );
					}
				}
			} else {
				$userAccounts[ $key ] = [ 'provider' => $item->provider, 'icon' => $item['icon'] ];
			}
		}

		return view( 'publishPost', [
			'userAccounts'               => $userAccounts,
			'user'                       => $this->userAuth,
			'post'                       => $post,
			'posted'                     => $posted,
			'subreddits'                 => $subreddits,
			'boards'                     => $boards,
			'userConnectedAccountsCount' => $userConnectedAccountsCount
		] );

	}

	public function publishPostsAction( Request $request ) {
		$connected = $request->connected;
		if ( isset( $connected ) ) {
			if ( isset( $request->timezone ) && $request->timezone != null ) {
				$request->updated_at = Carbon::now( 'UTC' )->addHour( $request->timezone );
			} else {
				$request->updated_at = Carbon::now();
				$request->timezone   = null;
			}
			$socialClass               = new SocialController();
			$scheduleClass             = new ScheduleController();

			$check_connected_instagram = array_search( 'instagram', $connected );

			if ( isset( $request->postImage ) ) {
				$request->img_link = url( $request->postImage );
				$request->img      = $request->postImage;
			} else {
				$request->img_link = null;
				$request->img      = null;
			}
			if ( $check_connected_instagram != false || $check_connected_instagram == 0 ) {
				$request->img_link_ins = $request->postImage;
			} else {
				$request->img_link_ins = null;
			}
			if ( isset( $request->boards_id ) && $request->boards_id != "" ) {
				$request->boards = $request->boards_id;
			} else {
				$request->boards = null;
			}
			if ( isset( $request->subreddits_id ) && $request->subreddits_id != "" ) {
				$request->subreddits = $request->subreddits_id;
			} else {
				$request->subreddits = null;
			}
			$suc_mes      = [];
			$suc_schedule = [];

			foreach ( $connected as $key => $item ) {
				if ( $item != null ) {
					$request->social = $item;
				}
				if ( $item == "pinterest" || $item == "reddit" ) {
					if ( $item == "pinterest" && $request->boards == null && $request->subreddits != null ) {
						Session::flash( 'message_error', 'Warning! You have no boards in the (pinterest)' );

						return redirect()->back();
					}
					if ( $item == "reddit" && $request->subreddits == null && $request->boards != null ) {
						Session::flash( 'message_error', 'Warning! You don\'t have any subscriptions in (reddit)' );

						return redirect()->back();
					}
					if ( $item == "pinterest" && $request->img_link == null && $request->images[ $key ] == null ) {
						Session::flash( 'message_error', 'Warning! Your post in the (pinterest) not IMAGE' );

						return redirect()->back();
					}
					if ( $item == "reddit" && $request->url[ $key ] == null ) {
						Session::flash( 'message_error', 'Warning! URL is required in (reddit)' );

						return redirect()->back();
					}
					if ( $request->subreddits == null && $request->boards == null ) {
						Session::flash( 'message_error', 'Warning! You have no boards in the (pinterest) and You don\'t have any subscriptions in (reddit)' );

						return redirect()->back();
					}
				}
				if ( $item == "linkedin" && $request->url[ $key ] == null ) {
					Session::flash( 'message', 'Warning! URL is required in (linkedin)' );

					return redirect()->back();
				}
				if ( isset( $request->access_token[ $key ] ) && $request->access_token[ $key ] != "" ) {
					$request->token_soc = $request->access_token[ $key ];
				} else {
					$request->token_soc = null;
				}
				if ( isset( $request->access_token_secret[ $key ] ) && $request->access_token_secret[ $key ] != "" ) {
					$request->token_soc_sec = $request->access_token_secret[ $key ];
				} else {
					$request->token_soc_sec = null;
				}
				if ( isset( $request->prov_user_id[ $key ] ) && $request->prov_user_id[ $key ] != "" ) {
					$request->provUserId = $request->prov_user_id[ $key ];
				} else {
					$request->provUserId = null;
				}
				if ( isset( $request->postTitle ) && $request->postTitle != null ) {
					$request->message = $request->postTitle[ $key ];
				} else {
					$request->message = null;
				}
				if ( isset( $request->postContent ) && $request->postContent != null ) {
					$request->content_text = $request->postContent[ $key ];
				} else {
					$request->content_text = null;
				}
				if ( isset( $request->url[ $key ] ) && $request->url[ $key ] != "" ) {
					$request->link = $request->url[ $key ];
				} else {
					$request->link = null;
				}
				if ( isset( $request->images[ $key ] ) && $request->images[ $key ] != null ) {
					$filename                     = 'app/' . $request->images[ $key ]->store( $this->userID );
					$img                          = url( Storage::url( $filename ) );
					$img_ins                      = Storage::url( $filename );
					$request->img_upload_link     = $img;
					$request->img_upload          = $filename;
					$request->img_upload_link_ins = $img_ins;
				} else {
					$request->img_upload_link     = null;
					$request->img_upload_link_ins = null;
					$request->img_upload          = null;
				}
				if ( isset( $request->schedule_posts ) ) {
					$schedule = $scheduleClass->index( $this->userID, $request );
					array_push( $suc_schedule, $schedule );
				} else {
					$socials = $socialClass->$item( $req = null, $request );
					$res     = $socials->getData( 'result' )['result'];
					array_push( $suc_mes, $res );
				}
			} // end foreach
			if ( $suc_mes != [] ) {
				return redirect()->back()->with( 'share_message_result', $suc_mes );
			}
			if ( $suc_schedule != [] ) {
				return redirect()->back()->with( 'schedule_message_result', $suc_schedule );
			}
		}
	}

	public function editPost( $id ) {
		$post = $this->userAuth->posts()->where('id', $id)->first();
		if ($post != null && $this->userAuth->hasRole('owner')) {
			return view( 'post', [ 'post' => $post, 'user' => $this->userAuth ] );
		} else {
			if($this->userAuth->hasRole('guest')) {
				Session::flash( 'message_role', 'You have not access to edit post' );
			}
			return redirect( '/posts' );
		}
	}

	public function editPosted( $id ) {
		$post = Posted::select( "social.icon", "posted.*" )
		              ->leftJoin( 'social', 'social.provider', '=', 'posted.provider' )
		              ->where( 'posted.user_id', $this->userID )
		              ->where( 'posted.id', $id )
		              ->first();
		if ($post != null && $this->userAuth->hasRole('owner') ) {
			return view( 'posted', [ 'post' => $post, 'user' => $this->userAuth ] );
		} else {
			if($this->userAuth->hasRole('guest')) {
				Session::flash( 'message_role', 'You have not access to edit post' );
			}
			return redirect( '/' );
		}
	}

	public function deletePostImage( Request $request ) {
		$postImage = Post::where( 'id', $request->id )->update( [ 'img' => '' ] );
		if ( $postImage == 1 ) {
			File::delete( storage_path( $request->img_url ) );

			return 'success';
		} else {
			return 'faild';
		}
	}

	public function deletePost( $id, Request $request ) {
		if ( isset( $request->post ) && ( $request->post == 1 || $request->post == 2 ) ) {
			if ( $request->post == 2 ) {
				$post   = Post::where( 'id', $id )->first();
				$userID = $post->user_id;
				if ( $post->img != null ) {
					File::delete( storage_path( $post->img ) );
					$del_post = Post::where( 'id', $id )->delete();
					$posts    = Post::where( 'user_id', $userID )->get();
					if ( count( $posts ) == 0 ) {
						File::deleteDirectory( storage_path( '/app/' . $userID ) );
					} else {
						foreach ( $posts as $item ) {
							if ( $item['img'] == null ) {
								File::deleteDirectory( storage_path( '/app/' . $userID ) );
							}
						}
					}
				} else {
					$del_post = Post::where( 'id', $id )->delete();
				}
				if ( $del_post == 1 ) {
					return 'success';
				} else {
					return 'faild';
				}
			} elseif ( $request->post == 1 ) {

				$del_post = Posted::where( 'id', $id )->delete();
				if ( $del_post == 1 ) {
					return 'success';
				} else {
					return 'faild';
				}
			}
		}
	}

	public function network() {
		if($this->userAuth->hasRole('owner')) {
			$socials               = Social::get();
			$userConnectedAccounts = Oauth::select( 'oauth.*' )
			                              ->leftJoin( 'users', 'users.id', '=', 'oauth.user_id' )
											//->where('oauth.user_name',$this->userAuth->name)
				                           ->where( 'oauth.user_id', $this->userID )
			                              ->get()->keyBy( 'social_id' );
			$userAccounts          = array();
			foreach ( $socials as $key => $item ) {
				if ( isset( $userConnectedAccounts[ $item->id ] ) ) {
					$userAccounts[ $key ] = [
						'provider'            => $item->provider,
						'userId'              => $userConnectedAccounts[ $item->id ]->id,
						'provUserId'          => $userConnectedAccounts[ $item->id ]->provider_user_id,
						'icon'                => $item['icon'],
						'access_token'        => $userConnectedAccounts[ $item->id ]->access_token,
						'access_token_secret' => $userConnectedAccounts[ $item->id ]->access_token_secret,
						'first_name'          => $userConnectedAccounts[ $item->id ]->first_name,
						'last_name'           => $userConnectedAccounts[ $item->id ]->last_name,
					];
				} else {
					$userAccounts[ $key ] = [ 'provider' => $item->provider, 'icon' => $item['icon'] ];
				}
			}
			return view( 'network', [ 'user' => $this->userAuth, 'userAccounts' => $userAccounts ] );
		} else {
			Session::flash( 'message_role', 'You have not access to add or delete account' );
			return redirect()->back();
		}
	}

	public function policy() {
		return view( 'policy' );
	}

	public function settings() {
		return view( 'settings' );
	}

	public function accountUpdate($id, Request $request) {
		if ( $request && $request->change_account == "change" && is_numeric($id) && $id > 0 ) {
			$name     = $request->name;
			$email    = $request->email;
			$old_password = $request->old_password;
			$new_password = $request->new_password;
			$confirm_password = $request->confirm_password;
			if($new_password===$confirm_password){
				$check_email = User::where('email',$email)->where('id','<>',$this->userAuth->id)->first();
				if($check_email==null){
					$check_user = User::where('id',$this->userAuth->id)->first();
					if($check_user!=null && Hash::check($old_password,$check_user->password)){
						$password = Hash::make($new_password);
						$change_pass = User::where('id',$this->userAuth->id)
											->update([
												'name'=>$name,
												'email'=>$email,
												'password'=>$password
											]);
						if($change_pass==1){
							Auth::logout();
							Session::flash( 'message_success_chenge_account', 'Success account changed' );
							return redirect('/login');
						}
					}
				}else{
					Session::flash( 'message_error', 'This Email exists' );
					return redirect()->back();
				}
				
			}else{
				Session::flash( 'message_error', 'New Password and Confirm Password not equal' );
				return redirect()->back();
			}
		} else {
			Session::flash( 'message_error', 'Error!' );
			return redirect()->back();
		}
	}

	public function accountInvite($id, Request $request)
	{
		if ( $request && $request->invite == "invite" && is_numeric($id) && $id > 0 && $this->userAuth->hasRole('owner') ) {
			$subject = $request->invite_subject;
			$email   = $request->invite_email;
			$message = $request->invite_message;
			$token = str_random(64);
			$invite = App\Invite::create([
				'email'     => $email,
				'token'     => $token,
				'user_id'   => $id,
			]);
			if($invite) {
				$data = [
					'subject' => $subject,
					'email' => $email,
					'message' => $message,
					'token' => $token,
				];

				Mail::send('emails.send-invite', $data, function($msg) use($data)
				{
					$msg->to($data['email'])->subject($data['subject']);
				});
				Session::flash( 'message_success_invite', 'Your invites sended' );
				return redirect()->back();
			} else {
				Session::flash( 'message_error_invite', 'Your invites not sending' );
				return redirect()->back();
			}
		} else {
			return redirect()->back();
		}
	}
}