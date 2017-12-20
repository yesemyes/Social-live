<?php

namespace App\Http\Controllers\Auth;

require_once( base_path('socials/instagram/ins.php') );

use InstagramUpload;

use App\User;
use App\Social;
use App\Oauth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use DB;
//use Hash;
use JWTAuth;
use Illuminate\Support\Facades\Mail;

class OauthController extends Controller
{
	protected $userAuth;

	public function __construct()
	{
		$this->middleware(function ($request, $next) {
			$this->userAuth = Auth::user();
			return $next($request);
		});
	}

	public function loginWithFacebook(Request $request)
	{
		$code = $request->get('code');
		if( $this->userAuth == null ){
			$user_id = $request->get('user_id');
			$user_name = $request->get('user_name');
			if( isset($user_id) ) Session::put('api_user_id', $user_id);
			if( isset($user_name) ) Session::put('api_user_name', $user_name);
		}
		$fb = \OAuth::consumer('Facebook', 'https://ipisocial.iimagine.one/facebook/login');

		if ( ! is_null($code) ){
			$token = $fb->requestAccessToken($code);
			$result = json_decode($fb->request('/me?fields=id,first_name,last_name,name,email,gender,locale,picture'), true);
			$result['access_token'] = $token->getAccessToken();
			$result['access_token_secret'] = '';
			if( isset($result['access_token']) ) return $this->regApi($result,'facebook');
		}
		else{
			$error = $request->get('error');
			if( isset($error) && $error == "access_denied" && $this->userAuth != null ) return redirect('/networks');
			elseif( isset($error) && $error == "access_denied" && $this->userAuth == null ){
				if( Session::has('api_user_id') ){
					$api_user_id = Session::get('api_user_id');
					$api_user_url = User::select('user_url')->where('id',$api_user_id)->first();
					return redirect($api_user_url->user_url.'/wp-admin/admin.php?page=iio4social-network');
				}
			}else {
				$url = $fb->getAuthorizationUri();
				return redirect((string)$url);
			}
		}
	}

	public function loginWithGoogle(Request $request)
	{
		$code = $request->get('code');
		if( $this->userAuth == null ){
			$user_id = $request->get('user_id');
			$user_name = $request->get('user_name');
			if( isset($user_id) ) Session::put('api_user_id', $user_id);
			if( isset($user_name) ) Session::put('api_user_name', $user_name);
		}
		$googleService = \OAuth::consumer('Google','https://ipisocial.iimagine.one/google/login');
		if ( ! is_null($code)) {
			$token = $googleService->requestAccessToken($code);
			$result = json_decode($googleService->request('https://www.googleapis.com/oauth2/v1/userinfo'), true);
			$result['access_token'] = $token->getAccessToken();
			$result['access_token_secret'] = '';
			$result['first_name'] = $result['given_name'];
			$result['last_name'] = $result['family_name'];
			if(isset($result['access_token'])) return $this->regApi($result,'google');
		}
		else {
			$url = $googleService->getAuthorizationUri();
			return redirect((string)$url);
		}
	}

	public function loginWithTwitter(Request $request)
	{
		$token  = $request->get('oauth_token');
		$verify = $request->get('oauth_verifier');
		if( $this->userAuth == null ){
			$user_id = $request->get('user_id');
			$user_name = $request->get('user_name');
			if( isset($user_id) ) Session::put('api_user_id', $user_id);
			if( isset($user_name) ) Session::put('api_user_name', $user_name);
		}
		$tw = \OAuth::consumer('Twitter', 'https://ipisocial.iimagine.one/twitter/login');

		if ( ! is_null($token) && ! is_null($verify) ){
			$token = $tw->requestAccessToken($token, $verify);
			$result = json_decode($tw->request('account/verify_credentials.json'), true);
			$result['access_token'] = $token->getAccessToken();
			$result['access_token_secret'] = $token->getAccessTokenSecret();
			$result['first_name'] = $result['name'];
			$result['last_name'] = '';
			if( isset($result['access_token']) ) return $this->regApi( $result, 'twitter' );
		}else{
			$error = $request->get('denied');
			if( isset($error) && $error != null && $this->userAuth != null ) return redirect('/networks');
			elseif( isset($error) && $error != null && $this->userAuth == null ){
				if( Session::has('api_user_id') ){
					$api_user_id = Session::get('api_user_id');
					$api_user_url = User::select('user_url')->where('id',$api_user_id)->first();
					return redirect($api_user_url->user_url.'/wp-admin/admin.php?page=iio4social-network');
				}
			}else{
				$reqToken = $tw->requestRequestToken();
				$url = $tw->getAuthorizationUri(['oauth_token' => $reqToken->getRequestToken()]);
				return redirect((string)$url);
			}
		}
	}

	public function loginWithLinkedin(Request $request)
	{
		$userAuth = Auth::user();
		$code = $request->get('code');
		if( $userAuth == null ){
			$user_id = $request->get('user_id');
			$user_name = $request->get('user_name');
			if( isset($user_id) ) Session::put('api_user_id', $user_id);
			if( isset($user_name) ) Session::put('api_user_name', $user_name);
		}
		$linkedinService = \OAuth::consumer('Linkedin','https://ipisocial.iimagine.one/linkedin/login');

		if ( ! is_null($code) ){
			$token = $linkedinService->requestAccessToken($code);
			$result = json_decode($linkedinService->request('/people/~?format=json'), true);
			$result['access_token'] = $token->getAccessToken();
			$result['access_token_secret'] = '';
			$result['first_name'] = $result['firstName'];
			$result['last_name'] = $result['lastName'];

			if( isset($result['access_token']) ) return $this->regApi( $result, 'linkedin' );
		}else{
			$error = $request->get('error');
			if( isset($error) && $error == "access_denied" && $this->userAuth != null ) return redirect('/networks');
			elseif( isset($error) && $error == "access_denied" && $this->userAuth == null ){
				if( Session::has('api_user_id') ){
					$api_user_id = Session::get('api_user_id');
					$api_user_url = User::select('user_url')->where('id',$api_user_id)->first();
					return redirect($api_user_url->user_url.'/wp-admin/admin.php?page=iio4social-network');
				}
			} else {
				$url = $linkedinService->getAuthorizationUri(['state'=>'DCEEFWF45453sdffef424']);
				return redirect((string)$url);
			}
		}
	}

	/*public function loginWithInstagram(Request $request)
	{
		$code = $request->get('code');
		if( $this->userAuth == null ){
			$user_id = $request->get('user_id');
			$user_name = $request->get('user_name');
			if( isset($user_id) ) Session::put('api_user_id', $user_id);
			if( isset($user_name) ) Session::put('api_user_name', $user_name);
		}
		$instagramService = \OAuth::consumer('Instagram','https://ipisocial.iimagine.one/instagram/login');
		if ( ! is_null($code))
		{
			$state = isset($_GET['state']) ? $_GET['state'] : null;
			$token = $instagramService->requestAccessToken($code, $state);
			$result = json_decode($instagramService->request('users/self'), true);
			$result['access_token'] = $token->getAccessToken();
			$result['access_token_secret'] = '';
			$result['id'] = $result['data']['id'];
			$result['first_name'] = $result['data']['username'];
			$result['last_name'] = '';
			if(isset($result['access_token'])) return $this->regApi($result,'instagram');
		}
		else
		{
			$url = $instagramService->getAuthorizationUri();
			return redirect((string)$url);
		}
	}*/

	public function loginWithInstagram(Request $request)
	{
		$obj = new InstagramUpload();
		$obj->Login($request->username, $request->password);
		if(isset($obj->upload_id) && $obj->upload_id!=null)
		{
			$result['id'] = $obj->uid;
			$result['access_token'] = $request->password;
			$result['access_token_secret'] = '';
			$result['first_name'] = $request->username;
			$result['last_name'] = '';
			return $this->regApi($result,'instagram');
		}else{
			print_r($obj);
		}
	}

	public function loginWithReddit(Request $request)
	{
		$code = $request->get('code');
		if( $this->userAuth == null ){
			$user_id = $request->get('user_id');
			$user_name = $request->get('user_name');
			if( isset($user_id) ) Session::put('api_user_id', $user_id);
			if( isset($user_name) ) Session::put('api_user_name', $user_name);
		}
		$reddit = \OAuth::consumer('Reddit','https://ipisocial.iimagine.one/reddit/login');

		if ( ! is_null($code)){
			$state = isset($_GET['state']) ? $_GET['state'] : null;
			$token = $reddit->requestAccessToken($code, $state);
			$result = json_decode($reddit->request('api/v1/me.json'), true);
			$result['access_token'] = $token->getAccessToken();
			$result['access_token_secret'] = '';
			$result['first_name'] = $result['name'];
			$result['last_name'] = '';
			if( isset($result['access_token']) ) return $this->regApi( $result,'reddit' );
		}else{
			if (strpos($_SERVER["HTTP_REFERER"], 'https://ssl.reddit.com/') !== false && $this->userAuth != null) return redirect('/networks');
			elseif( strpos($_SERVER["HTTP_REFERER"], 'https://ssl.reddit.com/') !== false && $this->userAuth == null ){
				if( Session::has('api_user_id') ){
					$api_user_id = Session::get('api_user_id');
					$api_user_url = User::select('user_url')->where('id',$api_user_id)->first();
					return redirect($api_user_url->user_url.'/wp-admin/admin.php?page=iio4social-network');
				}
			}
			else {
				$url = $reddit->getAuthorizationUri();
				return redirect((string)$url);
			}
		}
	}

	public function loginWithPinterest(Request $request)
	{
		$code = $request->get('code');
		if( $this->userAuth == null ){
			$user_id = $request->get('user_id');
			$user_name = $request->get('user_name');
			if( isset($user_id) ) Session::put('api_user_id', $user_id);
			if( isset($user_name) ) Session::put('api_user_name', $user_name);
		}
		$pinterestService = \OAuth::consumer('Pinterest','https://ipisocial.iimagine.one/pinterest/login');

		if ( ! is_null($code)){
			$state = isset($_GET['state']) ? $_GET['state'] : null;
			$token = $pinterestService->requestAccessToken($code, $state);
			$result = json_decode($pinterestService->request('v1/me/'), true);
			$result['access_token'] = $token->getAccessToken();
			$result['access_token_secret'] = '';
			$result['first_name'] = $result['data']['first_name'];
			$result['last_name'] = $result['data']['last_name'];
			$result['id'] = $result['data']['id'];
			if( isset($result['access_token']) ) return $this->regApi( $result,'pinterest' );
		}else{
			if (strpos($_SERVER["HTTP_REFERER"], 'https://api.pinterest.com') !== false && $this->userAuth != null) return redirect('/networks');
			elseif( strpos($_SERVER["HTTP_REFERER"], 'https://api.pinterest.com') !== false && $this->userAuth == null ){
				if( Session::has('api_user_id') ){
					$api_user_id = Session::get('api_user_id');
					$api_user_url = User::select('user_url')->where('id',$api_user_id)->first();
					return redirect($api_user_url->user_url.'/wp-admin/admin.php?page=iio4social-network');
				}
			}
			else{
				$url = $pinterestService->getAuthorizationUri();
				return redirect((string)$url);
			}
		}
	}

	protected function regApi($data,$provider)
	{
		if( Session::has('api_user_id') ){
			$user_id = Session::get('api_user_id');
			$user = Session::get('api_user_name');
		}elseif( $this->userAuth != null ){
			$user_id = $this->userAuth->id;
			$user = $this->userAuth->name;
		}else{
			$user_id = null;
			$user = null;
		}
		$currentDate = date("Y-m-d H:i:s");
		$get_social_id = Social::where('provider',$provider)->first();
		$check_user_by_id = User::where('id',$user_id)->first();

		if( isset($check_user_by_id) && $check_user_by_id != null ){
			$check_oauth_by_userIdAndProvider = Oauth::leftJoin('users', 'oauth.user_id', '=', 'users.id')
																	->leftJoin('oauth_items','oauth_items.oauth_id','=','oauth.id')
			                                         ->where('oauth.user_id',$check_user_by_id->id)
			                                         ->where('oauth.user_name',$user)
			                                         ->where('oauth.provider',$provider)
			                                         ->where('oauth.provider_user_id',$data['id'])
			                                         ->first();
			if( isset($check_oauth_by_userIdAndProvider) && $check_oauth_by_userIdAndProvider != null ){
				$updexistsOauth = Oauth::where('user_id',$check_user_by_id->id)
				                       ->where('provider',$provider)
				                       ->where('provider_user_id',$data['id'])
				                       ->update([
					                       'access_token' => $data['access_token'],
					                       'updated_at' => $currentDate,
				                       ]);
				if( $this->userAuth == null ){
					return redirect($check_user_by_id->user_url.'/wp-admin/admin.php?page=iio4social-network');
				}else{
					return redirect('/networks');
				}

			}else{
				$check_oauth_by_provId = Oauth::leftJoin('users', 'oauth.user_id', '=', 'users.id')
				                                          ->where('oauth.user_id',$check_user_by_id->id)
																		->where('oauth.provider_user_id',$data['id'])
				                                          ->where('oauth.provider',$provider)
				                                          ->first();
				if( isset($check_oauth_by_provId) && $check_oauth_by_provId != null ){
					if( $this->userAuth == null ){
						return redirect($check_user_by_id->user_url.'/wp-admin/admin.php?page=iio4social-network&success=false');
					}else{
						return redirect('/networks');
					}
				}else{
					$insID = Oauth::insertGetId(
					[
						'user_id'            => $check_user_by_id->id,
						'user_name'          => $user,
						'first_name'         => $data['first_name'],
						'last_name'          => $data['last_name'],
						'provider_user_id'   => $data['id'],
						'provider'           => $provider,
						'access_token'       => $data['access_token'],
						'access_token_secret'=> $data['access_token_secret'],
						'created_at'         => $currentDate,
						'updated_at'         => $currentDate,
						'social_id'          => $get_social_id->id,
					]);
					if( $this->userAuth == null ){
						return redirect($check_user_by_id->user_url.'/wp-admin/admin.php?page=iio4social-network');
					}else{
						return redirect('/networks');
					}
				}
			}
		}else{
			return response(['result'=>false]);
		}

	}

	public function destroy( $id, Request $request )
	{
	    if ( $request->ajax() ) {
	    	$account = Oauth::where('id',$id)->delete();

	        return response(['message_success' => 'Product deleted', 'status' => 'success']);
	    }
	    return response(['message_error' => 'Failed deleting the product', 'status' => 'failed']);
	}

	public function checkEmail($id,$url)
	{
		$checkUser = DB::table('users')
		               ->where('remainder',$url)
							->get();
		if( md5($checkUser[0]->id) == $id )
		{
			return view( 'auth.setpass')->withId( $checkUser[0]->id );
		}
	}

	public function setPass(Request $request, $id)
	{
		if( $request->_token != null )
		{
			if( $request->password === $request->password_confirmation )
			{
				$upd_pass = DB::table('users')
				              ->where('id',$id)
				              ->update(['password' => bcrypt($request->password)]);
				if( $upd_pass )
				{
					return redirect('/login');
				}
			}
		}
	}
}