<?php

namespace App\Http\Controllers;

use App\Http\Controllers\SocialController;
use App\Http\Controllers\ScheduleController;
use App\User;
use App\Social;
use App\Oauth;
use DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App;
use App\Post;
use App\Posted;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class HomeController extends Controller{
	public function __construct() {
		$this->middleware( 'auth' );
	}

	public function index() {
		$user  = Auth::user();
		$posts = Posted::select( "social.icon", "posted.*" )
		               ->leftJoin( 'social', 'social.provider', '=', 'posted.provider' )
		               ->where( 'posted.user_id', $user->id )
		               ->orderBy( 'posted.id', 'desc' )
		               ->get();
		if ( count( $posts ) > 0 ) {
			return view( 'home', [ 'posts' => $posts, 'user' => $user ] );
		} else {
			return redirect( '/create-post' );
		}
	}

	public function createPost() {
		return view( 'createPost' );
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
		$user                       = Auth::user();
		$userConnectedAccounts      = Oauth::select( 'oauth.user_id' )
		                                   ->leftJoin( 'users', 'users.id', '=', 'oauth.user_id' )
		                                   ->where( 'oauth.user_name', $user->name )
		                                   ->where( 'oauth.user_id', $user->id )
		                                   ->get()->keyBy( 'social_id' );
		$userConnectedAccountsCount = count( $userConnectedAccounts );
		$posts                      = Post::where( 'user_id', $user->id )->orderBy( 'id', 'desc' )->get();
		if ( count( $posts ) > 0 ) {
			return view( 'posts', [
				'userConnectedAccountsCount' => $userConnectedAccountsCount,
				'posts'                      => $posts,
				'user'                       => $user
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
		$user = Auth::user();
		if ( $user != null ) {
			$socials = Social::get();
			if ( isset( $posted ) && $posted != null && $posted == "posted" ) {
				$post = Posted::where( 'user_id', $user->id )->where( 'id', $postID )->first();
			} else {
				$post = Post::where( 'user_id', $user->id )->where( 'id', $postID )->where( 'status', 1 )->first();
			}
			if ( $post == null ) {
				Session::flash( 'message_error', 'Warning! your post not published' );

				return redirect( '/edit-post/' . $postID );
			}
			$userConnectedAccounts      = Oauth::select( 'oauth.*' )
			                                   ->leftJoin( 'users', 'users.id', '=', 'oauth.user_id' )
			                                   ->where( 'oauth.user_name', $user->name )
			                                   ->where( 'oauth.user_id', $user->id )
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
				'user'                       => $user,
				'post'                       => $post,
				'posted'                     => $posted,
				'subreddits'                 => $subreddits,
				'boards'                     => $boards,
				'userConnectedAccountsCount' => $userConnectedAccountsCount
			] );
		} else {
			return redirect( '/login' );
		}
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
			$user                      = Auth::user();
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
					$filename                     = 'app/' . $request->images[ $key ]->store( $user->id );
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
					$schedule = $scheduleClass->index( $user->id, $request );
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
		$user = Auth::user();
		$post = Post::where( 'user_id', $user->id )->where( 'id', $id )->first();
		if ( $post != null ) {
			return view( 'post', [ 'post' => $post, 'user' => $user ] );
		} else {
			return redirect( '/posts' );
		}
	}

	public function editPosted( $id ) {
		$user = Auth::user();
		$post = Posted::select( "social.icon", "posted.*" )
		              ->leftJoin( 'social', 'social.provider', '=', 'posted.provider' )
		              ->where( 'posted.user_id', $user->id )
		              ->where( 'posted.id', $id )
		              ->first();
		if ( $post != null ) {
			return view( 'posted', [ 'post' => $post, 'user' => $user ] );
		} else {
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
				$post = Posted::where( 'id', $id )->first();
				/*
				$userID = $post->user_id;
				if( $post->img != null ){
					File::delete(storage_path($post->img));
					$del_post = Posted::where('id',$id)->delete();
					$posts = Posted::where('user_id',$userID)->get();
					if(count($posts) == 0){
						File::deleteDirectory(storage_path('/app/'.$userID));
					}else{
						foreach ($posts as $item){
							if($item['img'] == null){
								File::deleteDirectory(storage_path('/app/'.$userID));
							}
						}
					}
				}else */
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
		$user                  = Auth::user();
		$socials               = Social::get();
		$userConnectedAccounts = Oauth::select( 'oauth.*' )
		                              ->leftJoin( 'users', 'users.id', '=', 'oauth.user_id' )
			//->where('oauth.user_name',$user->name)
			                           ->where( 'oauth.user_id', $user->id )
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

		return view( 'network', [ 'user' => $user, 'userAccounts' => $userAccounts ] );
	}

	public function policy() {
		return view( 'policy' );
	}

	public function settings() {
		return view( 'settings' );
	}

	public function accountUpdate( $id, Request $request ) {
		if ( $request && $request->change_account == "change" ) {
			$name     = $request->name;
			$email    = $request->email;
			$password = $request->password;
			dd( $id );
		} else {
			Session::flash( 'msg', 'Error!' );

			return redirect()->back();
		}
	}
}