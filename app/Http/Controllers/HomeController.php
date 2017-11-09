<?php

namespace App\Http\Controllers;


use App\User;
use App\Social;
use App\Oauth;
use DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App;
use App\Post;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

use App\Http\Controllers\SocialController;

/*require_once( base_path('socials/pinterest/vendor/autoload.php') );

use Pinterest\Authentication as Pin;
use Pinterest\Http\BuzzClient as Buzz;
use Pinterest\App\Scope;
use Pinterest\Api as API;
use Pinterest\Image as pinIMG*/;

use Illuminate\Http\Request;

class HomeController extends Controller
{
	/**
	 * Create a new controller instance.
	 *
	 * @return void
	 */
	public function __construct()
	{
		$this->middleware('auth');
	}

	/**
	 * Show the application dashboard.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return view('home');
	}

	public function createPost()
	{
		return view('createPost');
	}

	public function createPostAction(Request $request)
	{
		$user = Auth::user();
		$title = $request->postTitle;
		$content = $request->postContent;
		$image = $request->image;
		if( !empty($title) ){
			if($image != null){
				$filename = 'app/'.$image->store($user['id']);
			}else{
				$filename = null;
			}
			Post::create([
				'user_id'=> $user['id'],
				'title'  => $title,
				'text'   => $content,
				'img'    => $filename,
			]);
			Session::flash('message', 'Post created!');
			return redirect('/manage-posts');
		}else{
			Session::flash('message', 'Post not created!');
			return redirect('/manage-posts');
		}
	}

	public function managePosts()
	{
		$user = Auth::user();
		$posts = Post::where('user_id',$user->id)->get();
		return view('posts',['posts'=>$posts,'user'=>$user]);
	}

	public function publishPost($postID)
	{
		$user = Auth::user();
		if( $user != null )
		{
			$socials = Social::get();
			$post    = Post::where('user_id',$user->id)->where('id',$postID)->first();
			$userConnectedAccounts = Oauth::select('oauth.*')
			                              ->leftJoin('users','users.id','=','oauth.user_id')
			                              ->where('oauth.user_name',$user->name)
			                              ->where('oauth.user_id',$user->id)
			                              ->get()->keyBy('social_id');
			$userAccounts = array();
			$socialClass = new SocialController();
			$subreddits = "";
			$boards = "";
			foreach($socials as $key => $item)
			{
				if( isset($userConnectedAccounts[$item->id]) )
				{
					$userAccounts[$key] = [
						'provider'           => $item->provider,
						'userId'             => $userConnectedAccounts[$item->id]->id,
						'provUserId'         => $userConnectedAccounts[$item->id]->provider_user_id,
						'icon'               => $item['icon'],
						'access_token'       => $userConnectedAccounts[$item->id]->access_token,
						'access_token_secret'=> $userConnectedAccounts[$item->id]->access_token_secret,
						'first_name'         => $userConnectedAccounts[$item->id]->first_name,
						'last_name'          => $userConnectedAccounts[$item->id]->last_name,
					];
					if( isset($userConnectedAccounts[$item->id]->access_token) && $userConnectedAccounts[$item->id]->access_token != "" ) $token = $userConnectedAccounts[$item->id]->access_token;
					else $token = null;
					if( isset($item->provider) && $item->provider == "reddit" ){
						$get_subreddits = $socialClass->get_subreddits_web($token);
						if( isset($get_subreddits->message) && $get_subreddits->message == "Unauthorized" ) $subreddits = null;
						else $subreddits = $socialClass->get_subreddits_web($token);
					}
					if( isset($item->provider) && $item->provider == "pinterest" ){
						$get_boards = $socialClass->get_boards_web($token);
						if($get_boards == []) $boards = null;
						else $boards = $socialClass->get_boards_web($token);
					}
				}else $userAccounts[$key] = ['provider' => $item->provider,'icon' => $item['icon']];
			}

			return view('publishPost',[
				'userAccounts' => $userAccounts,
            'user'         => $user,
				'post'         => $post,
				'subreddits'   => $subreddits,
				'boards'       => $boards
			]);
		}else return redirect('/login');
	}

	public function publishPostsAction(Request $request)
	{
		$connected = $request->connected;
		if( isset($connected) )
		{
			if( isset($request->postImage) ) $request->img_link = url($request->postImage);
			else $request->img_link = null;
			if( isset($request->boards_id) && $request->boards_id != "" ) $request->boards = $request->boards_id;
			else $request->boards = null;
			if( isset($request->subreddits_id) && $request->subreddits_id != "" ) $request->subreddits = $request->subreddits_id;
			else $request->subreddits = null;
			$socialClass = new SocialController();
			$user = Auth::user();
			$connected 	= $request->connected;
			foreach($connected as $key => $item)
			{
				if( $item == "pinterest" || $item == "reddit" ){
					if( $request->boards == null && $request->subreddits != null ){
						Session::flash('message', 'Warning! You have no boards in the (pinterest)');
						return redirect()->back();
					}
					elseif( $request->subreddits == null && $request->boards != null ){
						Session::flash('message', 'Warning! You don\'t have any subscriptions in (reddit)');
						return redirect()->back();
					}
					elseif( $request->subreddits == null && $request->boards == null ){
						Session::flash('message', 'Warning! You have no boards in the (pinterest) and You don\'t have any subscriptions in (reddit)');
						return redirect()->back();
					}
				}
				if( isset($request->access_token[$key]) && $request->access_token[$key] != "" ) $request->token_soc = $request->access_token[$key];
				else $request->token_soc = null;
				if( isset($request->access_token_secret[$key]) && $request->access_token_secret[$key] != "" ) $request->token_soc_sec = $request->access_token_secret[$key];
				else $request->token_soc_sec = null;
				if( isset($request->postTitle) && $request->postTitle != null ) $request->message = $request->postTitle[$key];
				else $request->message = null;
				if( isset($request->postContent) && $request->postContent != null ) $request->content_text  = $request->postContent[$key];
				else $request->content_text = null;
				if( isset($request->url[$key]) && $request->url[$key] != "" ) $request->link = $request->url[$key];
				else $request->link = null;
				if( isset($request->images[$key]) && $request->images[$key] != null ){
					$filename = 'app/'.$request->images[$key]->store($user->id);
					$img = url(Storage::url($filename));
					$request->img_upload_link  = $img;
				}else$request->img_upload_link = null;
				$socials = $socialClass->$item($request);
			}
			if( $socials->getData('result')['result'] == "success" ){
				Session::flash('message', 'Your post(s) successful created!');
				return redirect()->back();
			}else{
				Session::flash('message', 'Error !');
				return redirect()->back();
			}
		}
	}

	public function editPost($id)
	{
		$user = Auth::user();
		$post = Post::where('user_id',$user->id)->where('id',$id)->first();
		return view('post',['post'=>$post, 'user'=>$user]);
	}

	public function deletePostImage(Request $request)
	{
		$postImage = Post::where('id',$request->id)->update([ 'img' => '' ]);
		if( $postImage == 1 ){
			File::delete(storage_path($request->img_url));
			return 'success';
		}
		else return 'faild';
	}

	public function editPostAction($id, Request $request)
	{
		$user = Auth::user();
		$title = $request->postTitle;
		$content = $request->postContent;
		$image = $request->image;
		if( !empty($title) ) {
			if ( $image != null ) {
				$filename = 'app/' . $image->store( $user['id'] );
			} else {
				$filename = null;
			}
			$post = Post::where('id',$id)
						            ->update([
							            'title'  => $title,
							            'text'   => $content,
							            'img'    => $filename,
						            ]);
			if( $post == 1 ){
				if( isset($request->postImgOldUrl) ){
					File::delete(storage_path($request->postImgOldUrl));
				}
				Session::flash('message', 'Post updated!');
				return redirect()->back();
			}else{
				Session::flash('message', 'Post not updated!');
				return redirect()->back();
			}
		}else{
			Session::flash('message', 'Post not updated!');
			return redirect()->back();
		}

	}

	public function deletePost($id)
	{
		$post = Post::where('id',$id)->first();
		$userID = $post->user_id;
		if( $post->img != null ){
			File::delete(storage_path($post->img));
			$del_post = Post::where('id',$id)->delete();
			$posts = Post::where('user_id',$userID)->get();
			if(count($posts) == 0){
				File::deleteDirectory(storage_path('/app/'.$userID));
			}else{
				foreach ($posts as $item){
					if($item['img'] == null){
						File::deleteDirectory(storage_path('/app/'.$userID));
					}
				}
			}
		}else $del_post = Post::where('id',$id)->delete();

		if( $del_post == 1 ) return 'success';
		else return 'faild';
	}

	public function network()
	{
		$user = Auth::user();
		$socials = Social::get();
		/*$userConnectedAccounts = $user->connectedAccounts()->get()->keyBy('social_id');

		$userAccounts = array();
		foreach($socials as $key => $item) {

			if( isset($userConnectedAccounts[$item->id]) ){
				$userID = $userConnectedAccounts[$item->id]->id;
				$provUserID = $userConnectedAccounts[$item->id]->provider_user_id;
				$access_token = $userConnectedAccounts[$item->id]->access_token;
				$userAccounts[$key] = [
					'provider' => $item->provider,
					'userId' => $userID,
					'provUserId' => $provUserID,
					'icon' => $item['icon'],
					'access_token' => $access_token
				];
			}else{
				$userAccounts[$key] = ['provider' => $item->provider,'icon' => $item['icon']];
			}
		}*/
		$userConnectedAccounts = Oauth::select('oauth.*')
		                              ->leftJoin('users','users.id','=','oauth.user_id')
		                              ->where('oauth.user_name',$user->name)
		                              ->where('oauth.user_id',$user->id)
		                              ->get()->keyBy('social_id');

		$userAccounts = array();
		foreach($socials as $key => $item) {
			if( isset($userConnectedAccounts[$item->id]) ){
				$userAccounts[$key] = [
					'provider'           => $item->provider,
					'userId'             => $userConnectedAccounts[$item->id]->id,
					'provUserId'         => $userConnectedAccounts[$item->id]->provider_user_id,
					'icon'               => $item['icon'],
					'access_token'       => $userConnectedAccounts[$item->id]->access_token,
					'access_token_secret'=> $userConnectedAccounts[$item->id]->access_token_secret,
					'first_name'         => $userConnectedAccounts[$item->id]->first_name,
					'last_name'          => $userConnectedAccounts[$item->id]->last_name,
				];
			}
			else{
				$userAccounts[$key] = ['provider' => $item->provider,'icon' => $item['icon']];
			}
		}
		return view('network', ['user' => $user, 'userAccounts' => $userAccounts]);
	}

	public function policy()
	{
		return view('policy');
	}

	public function Account()
	{
		return view('account');
	}

	public function accountUpdate($id, Request $request)
	{
		if($request && $request->change_account == "change")
		{
			$name = $request->name;
			$email = $request->email;
			$password = $request->password;
			dd($id);
		}
		else
		{
			Session::flash('msg','Error!');
			return redirect()->back();
		}

	}
}
