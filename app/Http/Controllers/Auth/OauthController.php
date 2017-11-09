<?php

namespace App\Http\Controllers\Auth;

require_once( base_path('socials/pinterest/vendor/autoload.php') );

use Pinterest\Authentication as Pin;
use Pinterest\Http\BuzzClient as Buzz;
use Pinterest\App\Scope;
use Pinterest\Api as API;
use Pinterest\Image as pinIMG;

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
			if( isset($_GET['user_id']) ) Session::put('api_user_id', $_GET['user_id']);
			if( isset($_GET['user_name']) ) Session::put('api_user_name', $_GET['user_name']);
		}
		$fb = \OAuth::consumer('Facebook', 'https://ipisocial.iimagine.one/facebook/login');

		if ( ! is_null($code) ){
			$token = $fb->requestAccessToken($code);
			$result = json_decode($fb->request('/me?fields=id,first_name,last_name,name,email,gender,locale,picture'), true);
			$result['access_token'] = $token->getAccessToken();
			$result['access_token_secret'] = '';

			if( isset($result['access_token']) ){
				return $this->regApi($result,'facebook');
			}
		}
		else{
			$url = $fb->getAuthorizationUri();
			return redirect((string)$url);
		}
	}

	public function loginWithGoogle(Request $request)
	{
		// get data from request
		$code = $request->get('code');
		// get google service
		if( isset($_GET['wp']) ) {
			$wp = $_GET['wp'];
			if( isset($_GET['user_id']) ) Session::put('api_user_id', $_GET['user_id']);
			if( isset($_GET['user_name']) ) Session::put('api_user_name', $_GET['user_name']);
			$googleService = \OAuth::consumer('Google','https://ipisocial.iimagine.one/google/login/?wp=true');
		}else{
			$wp = null;
			$googleService = \OAuth::consumer('Google','https://ipisocial.iimagine.one/google/login');
		}
		// check if code is valid

		// if code is provided get user data and sign in
		if ( ! is_null($code))
		{
			// This was a callback request from google, get the token
			$token = $googleService->requestAccessToken($code);
			// Send a request with it
			$result = json_decode($googleService->request('https://www.googleapis.com/oauth2/v1/userinfo'), true);
			$result['access_token'] = $token->getAccessToken();
			$result['access_token_secret'] = '';

			if(isset($result['access_token'])) {
				$result['first_name'] = $result['given_name'];
				$result['last_name'] = $result['family_name'];
				return $this->regApi($result,'google', $wp);
			}
			/*if( $result['verified_email'] == true ) {
				return $this->_register($result,'google');
			}*/
		}
		// if not ask for permission first
		else
		{
			// get googleService authorization
			$url = $googleService->getAuthorizationUri();
			// return to google login url
			return redirect((string)$url);
		}
	}

	public function loginWithTwitter(Request $request)
	{
		$token  = $request->get('oauth_token');
		$verify = $request->get('oauth_verifier');
		if( $this->userAuth == null ){
			if( isset($_GET['user_id']) ) Session::put('api_user_id', $_GET['user_id']);
			if( isset($_GET['user_name']) ) Session::put('api_user_name', $_GET['user_name']);
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
			$reqToken = $tw->requestRequestToken();
			$url = $tw->getAuthorizationUri(['oauth_token' => $reqToken->getRequestToken()]);
			return redirect((string)$url);
		}
	}

	public function loginWithLinkedin(Request $request)
	{
		$userAuth = Auth::user();
		$code = $request->get('code');
		if( $userAuth == null ){
			if( isset($_GET['user_id']) ) Session::put('api_user_id', $_GET['user_id']);
			if( isset($_GET['user_name']) ) Session::put('api_user_name', $_GET['user_name']);
		}
		$linkedinService = \OAuth::consumer('Linkedin','https://ipisocial.iimagine.one/linkedin/login');

		if ( ! is_null($code) ){
			$token = $linkedinService->requestAccessToken($code);
			$result = json_decode($linkedinService->request('/people/~?format=json'), true);
			$result['access_token'] = $token->getAccessToken();
			$result['access_token_secret'] = '';
			$result['first_name'] = $result['firstName'];
			$result['last_name'] = $result['lastName'];

			if( isset($result['access_token']) ){
				return $this->regApi( $result, 'linkedin' );
			}
		}else{
			$url = $linkedinService->getAuthorizationUri(['state'=>'DCEEFWF45453sdffef424']);
			return redirect((string)$url);
		}
	}

	public function loginWithInstagram(Request $request)
	{
		// get data from request
		$code = $request->get('code');
		if( isset($_GET['wp']) ) {
			$wp = $_GET['wp'];
			if( isset($_GET['user_id']) ) Session::put('api_user_id', $_GET['user_id']);
			if( isset($_GET['user_name']) ) Session::put('api_user_name', $_GET['user_name']);
			$instagramService = \OAuth::consumer('Instagram','https://ipisocial.iimagine.one/instagram/login/?wp=true');
		}else{
			$instagramService = \OAuth::consumer('Instagram','https://ipisocial.iimagine.one/instagram/login');
		}
		if ( ! is_null($code))
		{
			$state = isset($_GET['state']) ? $_GET['state'] : null;
			// This was a callback request from Instagram, get the token

			$token = $instagramService->requestAccessToken($code, $state);

			// Send a request with it. Please note that XML is the default format.
			$result = json_decode($instagramService->request('users/self'), true);

			$result['access_token'] = $token->getAccessToken();

			$result['access_token_secret'] = '';

			if(isset($_GET['wp'])) {
				$result['id'] = $result['data']['id'];
				$result['first_name'] = $result['data']['username'];
				$result['last_name'] = '';
				return $this->regApi($result,'instagram', $wp);
			}
			if( isset($result['access_token']) ) {
				return $this->_register($result,'instagram');
			}
		}
		// if not ask for permission first
		else
		{
			// get instagramService authorization
			$url = $instagramService->getAuthorizationUri();

			// return to instagram login url
			return redirect((string)$url);
		}
	}

	public function loginWithReddit(Request $request)
	{
		$code = $request->get('code');
		if( $this->userAuth == null ){
			if( isset($_GET['user_id']) ) Session::put('api_user_id', $_GET['user_id']);
			if( isset($_GET['user_name']) ) Session::put('api_user_name', $_GET['user_name']);
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
			$url = $reddit->getAuthorizationUri();
			return redirect((string)$url);
		}
	}

	public function loginWithPinterest(Request $request)
	{
		$code = $request->get('code');
		if( $this->userAuth == null ){
			if( isset($_GET['user_id']) ) Session::put('api_user_id', $_GET['user_id']);
			if( isset($_GET['user_name']) ) Session::put('api_user_name', $_GET['user_name']);
		}
		$pinterestService = \OAuth::consumer('Pinterest','https://ipisocial.iimagine.one/pinterest/login');

		if ( ! is_null($code)){
			$state = isset($_GET['state']) ? $_GET['state'] : null;
			$token = $pinterestService->requestAccessToken($code, $state);
			$result = json_decode($pinterestService->request('v1/me/'), true);
			$result['access_token'] = $token->getAccessToken();
			$result['access_token_secret'] = '';
			/* get boards ids */
			/*$client = new Buzz;
			$auth = Pin::onlyAccessToken($client, $result['access_token']);
			$api = new API($auth);
			$response = $api->getUserBoards();
			if ($response->ok()){
				$pagedList = $response->result();
				$boards = $pagedList->items();
			}else{
				$boards = null;
			}
			$result['boards'] = $boards;*/
			/* end boards ids */
			$result['first_name'] = $result['data']['first_name'];
			$result['last_name'] = $result['data']['last_name'];
			$result['id'] = $result['data']['id'];
			if( isset($result['access_token']) ) return $this->regApi( $result,'pinterest' );
		}else{
			$url = $pinterestService->getAuthorizationUri();
			return redirect((string)$url);
		}
	}

	/*protected function _register($data,$provider,$wp = null)
	{
		$member = Auth::user();

		$currentDate = date("Y-m-d H:i:s");
		$get_social_id = Social::where('provider',$provider)->first();

		if( isset($data['email']) && $data['email'] != null ) {
			$check_user_by_email = User::where('email',$data['email'])->first(); // fb, google
		}
		else {
			$data['email'] = $data['id'];
			$check_user_by_email = null;
		}

		if( isset($check_user_by_email) && $check_user_by_email != null ) {
			$check_oauth_by_userIdAndProvider = Oauth::leftJoin('users', 'oauth.user_id', '=', 'users.id')
						->where('oauth.user_id',$check_user_by_email->id)
						->where('oauth.provider',$provider)
						->first();
		}else {
			$check_oauth_by_userIdAndProvider = Oauth::leftJoin('users', 'oauth.user_id', '=', 'users.id')
			                                      ->where('oauth.provider_user_id',$data['id'])
			                                      ->where('oauth.provider',$provider)
			                                      ->first();
		}
	// CHECKS ALL VARIANTS
		if( $check_user_by_email == null && $member == null && $check_oauth_by_userIdAndProvider == null )
		{
			$ins_user = User::insertGetId(
			[
				'name'            => $data['name'],
				'email'           => $data['email'],
				'password'        => '',
				'remember_token'  => '',
				'created_at'      => $currentDate,
				'updated_at'      => $currentDate,
			]);
			if($ins_user != 0) {
				$get_last_user = User::where('id',$ins_user)->first();
				DB::table('oauth')->insert(
					[
						'user_id'         => $ins_user,
						'provider_user_id'=> $data['id'],
						'provider'        => $provider,
						'access_token'    => isset($data['access_token']) ? $data['access_token'] : '',
						'created_at'      => $currentDate,
						'updated_at'      => $currentDate,
						'social_id'       =>  $get_social_id->id,
					]);
				if( $wp == 'true' ) {
					Auth::login($get_last_user);
					//$result = @$data['name'].'&email='.@$data['email'].'&id='.$data['id'];
					return redirect('http://localhost/blog-to-social/wp-admin/admin.php?page=iio4social-network');
				}else {
					Auth::login($get_last_user);
					return redirect('/home');
				}
			}
		}
		elseif( $check_user_by_email != null && $member == null )
		{
			//return response(['asd'=>$check_user_by_email]);
			if( $check_oauth_by_userIdAndProvider == null ) {
				Oauth::insert(
				[
					'user_id'            => $check_user_by_email->id,
					'provider_user_id'   => $data['id'],
					'provider'           => $provider,
					'access_token'       => isset($data['access_token']) ? $data['access_token'] : '',
					'created_at'         => $currentDate,
					'updated_at'         => $currentDate,
					'social_id'          => $get_social_id->id,
				]);
				if( $wp == 'true' ) {
					Auth::login($check_user_by_email);
					//$result = @$data['name'].'&email='.@$data['email'].'&id='.$data['id'];
					return redirect('http://localhost/blog-to-social/wp-admin/admin.php?page=iio4social-network');
				}else {
					Auth::login($check_user_by_email);
					return redirect('/home');
				}
			}
			else {
				$updexistsOauth = Oauth::
											where('user_id',$check_user_by_email->id)
				   	               ->where('provider',$provider)
											->where('provider_user_id',$data['id'])
											->update([
												'access_token' => $data['access_token'],
												'updated_at' => $currentDate,
											]);
				if( $updexistsOauth == 1 ) {
					if( $wp == 'true' ) {
						Auth::login($check_user_by_email);
						//$result = @$data['name'].'&email='.@$data['email'].'&id='.$data['id'];
						return redirect('http://localhost/blog-to-social/wp-admin/admin.php?page=iio4social-network');
					}else {
						Auth::login($check_user_by_email);
						return redirect('/home');
					}
				}
			}
		}
		elseif( $check_oauth_by_userIdAndProvider != null && $member == null )
		{
			$get_user_by_oauth = User::select('users.*')
												->leftJoin('oauth', 'users.id', '=', 'oauth.user_id')
												->where('users.id',$check_oauth_by_userIdAndProvider->id)
												->first();
			$updexistsOauth = Oauth::
										where('user_id',$get_user_by_oauth->id)
						            ->where('provider',$provider)
										->where('provider_user_id',$data['id'])
										->update([
												'access_token' => $data['access_token'],
												'updated_at' => $currentDate,
										]);

			if( $updexistsOauth == 1 ) {
				if( $wp == 'true' ) {
					Auth::login($get_user_by_oauth);
					$result = $data['name'].'&email='.$data['email'].'&id='.$data['id'];
					return redirect('http://localhost/blog-to-social/wp-admin/admin.php?page=iio4social-network');
				}else {
					Auth::login($get_user_by_oauth);
					return redirect('/home');
				}
			}
		}
		elseif( $member != null )
		{
			if( $check_oauth_by_userIdAndProvider == null ) {
				//Session::flash('message', 'this account '.$provider.' already exists please add your '.$provider.' in dashboard');
				Oauth::insert(
				[
					'user_id'          => $member->id,
					'provider_user_id' => $data['id'],
					'provider'         => $provider,
					'access_token'     => isset($data['access_token']) ? $data['access_token'] : '',
					'created_at'       => $currentDate,
					'updated_at'       => $currentDate,
					'social_id'        =>  $get_social_id->id,
				]);
				if( $wp == 'true' ) {
					Auth::login($member);
					$result = $data['name'].'&email='.$data['email'].'&id='.$data['id'];
					return redirect('http://localhost/blog-to-social/wp-admin/admin.php?page=iio4social-network');
				}else{
					Auth::login($member);
					return redirect('/home');
				}

			}else {
				$updexistsOauth = Oauth::
											where('user_id',$member->id)
				   	               ->where('provider',$provider)
											->where('provider_user_id',$data['id'])
											->update([
												'access_token' => $data['access_token'],
												'updated_at' => $currentDate,
											]);

				if( $updexistsOauth == 1 ) {
					if( $wp == 'true' ) {
						Auth::login($member);

						//$result = @$data['name'].'&email='.@$data['email'].'&id='.$data['id'];
						return redirect('http://localhost/blog-to-social/wp-admin/admin.php?page=iio4social-network');
					}else {
						Auth::login($member);
						return redirect('/home');
					}
				}
			}
		}
		else
		{
			Session::flash('message', 'undefined error!');
			if( $wp == 'true' ) {
				//$result = @$data['name'].'&email='.@$data['email'].'&id='.$data['id'];
				return redirect('http://localhost/blog-to-social/wp-admin/admin.php?page=iio4social-network');
			}else{
				return redirect('/home');
			}
		}



	// END
	}*/

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

	        return response(['message' => 'Product deleted', 'status' => 'success']);
	    }
	    return response(['message' => 'Failed deleting the product', 'status' => 'failed']);   
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