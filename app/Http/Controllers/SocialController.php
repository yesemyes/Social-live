<?php

namespace App\Http\Controllers;
session_start();

require_once( base_path('socials/facebook/fbsdk/src/Facebook/autoload.php') );
require_once( base_path('socials/twitter/TwitterAPIExchange.php') );
require_once( base_path('socials/linkedin/LinkedIn/LinkedIn.php') );
//require_once( base_path('socials/link/vendor/autoload.php') );
require_once( base_path('socials/reddit/reddit.php') );
require_once( base_path('socials/pinterest/vendor/autoload.php') );
//require_once( base_path('socials/instagram/instagram_post.php') );
require_once( base_path('socials/instagram/ins.php') );
require_once( base_path('socials/google/vendor/autoload.php') );

use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;

use TwitterAPIExchange;

use LinkedIn\LinkedIn;
//use Happyr\LinkedIn\LinkedIn as link;

use reddit;

//use instagram_post;
use InstagramUpload;

use Pinterest\Authentication as Pin;
use Pinterest\Http\BuzzClient as Buzz;
use Pinterest\App\Scope;
use Pinterest\Api as API;
use Pinterest\Image as pinIMG;

use Google_Client;
use Google_Service_Plus;
use Aws\S3\S3Client;



use Illuminate\Http\Request;

class SocialController extends Controller
{
	public $fb;
	public $twitter;
	public $li;
	public $reddit;
	public $pin;

	public function facebook(Request $request)
	{
		$this->fb = new Facebook([
			'app_id'                => '136738110284510',
			'app_secret'            => '333cc418895ad004074aeaac05ad5f5c',
			'default_graph_version' => 'v2.5',
		]);
		if( $request->img_upload_link != null ){
			$request->img_upload_link = str_replace('https://', 'http://', $request->img_upload_link );
			$linkData = [
				'link' => $request->link,
				'message' => $request->message,
				'source' => $this->fb->fileToUpload($request->img_upload_link),
			];
		}
		elseif($request->img_link != null){
			$request->img_link = str_replace('https://', 'http://', $request->img_link );
			$linkData = [
				'link' => $request->link,
				'message' => $request->message,
				'source' => $this->fb->fileToUpload($request->img_link),
			];
		}
		else{
			$linkData = [
				'link' => $request->link,
				'message' => $request->message,
			];
		}

		try {
			if( $request->img_link != null || $request->img_upload_link != null )
				$response = $this->fb->post('/me/photos', $linkData, $request->token_soc);
			else
				$response = $this->fb->post('/me/feed', $linkData, $request->token_soc);
		} catch(FacebookResponseException $e) {
			echo 'Graph returned an error: ' . $e->getMessage();
			exit;
		} catch(FacebookSDKException $e) {
			echo 'Facebook SDK returned an error: ' . $e->getMessage();
			exit;
		}
		$graphNode = $response->getGraphNode();
		if( $graphNode != null ){
			return response()->json(['result'=>'SUCCESS! your post in Facebook now shared']);
		}else{
			return response()->json(['result'=>'ERROR! Facebook share']);
		}
	}

	public function twitter(Request $request)
	{
		$settings = array(
			'oauth_access_token' => $request->token_soc, // request->access_token
			'oauth_access_token_secret' => $request->token_soc_sec, // request->access_token_secret
			'consumer_key' => "jc07IXLaF7F8rs7yQFYQ9SYHD",
			'consumer_secret' => "WvZCLGkYZnTEMkeTDDgCwnmP3gb4opVZBmQaTBwroQr0numo7f"
		);
		$this->twitter = new TwitterAPIExchange($settings);
		/** URL for REST request, see: https://dev.twitter.com/docs/api/1.1/ **/

		if($request->img_upload_link != null){
			$request->img_upload_link = str_replace('https://', 'http://', $request->img_upload_link );

			$file = file_get_contents($request->img_upload_link);

			$data = base64_encode($file);
			$url = "https://upload.twitter.com/1.1/media/upload.json";
			$method = 'POST';
			$params = array(
				"media_data" => $data,
			);
			$json = $this->twitter
				->buildOauth($url, $method)
				->setPostfields($params)
				->performRequest();
			// Result is a json string
			$res = json_decode($json);
			// Extract media id

			$id = $res->media_id_string;
			$url = 'https://api.twitter.com/1.1/statuses/update.json';
			$requestMethod = 'POST';
			$postfields = array(
				'media_ids' => $id,
				'status' => $request->message.' '.$request->link );
			$response = $this->twitter->buildOauth($url, $requestMethod)
			                          ->setPostfields($postfields)
			                          ->performRequest();
		}
		elseif($request->img_link != null){
			$request->img_link = str_replace('https://', 'http://', $request->img_link );
			$file = file_get_contents($request->img_link);
			$data = base64_encode($file);
			$url = "https://upload.twitter.com/1.1/media/upload.json";
			$method = 'POST';
			$params = array(
				"media_data" => $data,
			);
			$json = $this->twitter
				->buildOauth($url, $method)
				->setPostfields($params)
				->performRequest();
			// Result is a json string
			$res = json_decode($json);
			// Extract media id

			$id = $res->media_id_string;
			$url = 'https://api.twitter.com/1.1/statuses/update.json';
			$requestMethod = 'POST';
			$postfields = array(
				'media_ids' => $id,
				'status'    => $request->message.' '.$request->link
			);
			$response = $this->twitter->buildOauth($url, $requestMethod)
			                          ->setPostfields($postfields)
			                          ->performRequest();
		}
		else{
			$url = 'https://api.twitter.com/1.1/statuses/update.json';
			$requestMethod = 'POST';
			$postfields = array(
				'status' => $request->message.' '.$request->link );
			$response = $this->twitter->buildOauth($url, $requestMethod)
			                          ->setPostfields($postfields)
			                          ->performRequest();
		}
		$response = json_decode($response, true);
		if( isset($response['id']) && $response['id'] != null ){
			return response()->json(['result'=>'SUCCESS! your post in Twitter now shared']);
		}elseif( isset($response['errors']) ){
			return response()->json(['result'=>'ERROR! Twitter share']);
		}
	}

	public function linkedin(Request $request)
	{
		$this->li = new LinkedIn(
			array(
				'api_key' => '77bxo3m22s83c2',
				'api_secret' => 'POVE4Giqvd4DlTnU',
				'callback_url' => 'https://ipisocial.iimagine.one/linkedin/login'
			)
		);

		$this->li->setAccessToken($request->token_soc);

		if($request->img_upload_link != null){
			$request->img_upload_link = str_replace('https://', 'http://', $request->img_upload_link );
			$postParams = array(
				"content" => array(
					"title" 		=> $request->message,
					"description" 	=> $request->content_text,
					"submitted-url" => $request->link,
					"submitted-image-url" => $request->img_upload_link,
				),
				"visibility" => array(
					"code" => "anyone"
				)
			);
		}elseif($request->img_link != null){
			$request->img_link = str_replace('https://', 'http://', $request->img_link );
			$postParams = array(
				"content" => array(
					"title" 		=> $request->message,
					"description" 	=> $request->content_text,
					"submitted-url" => $request->link,
					"submitted-image-url" => $request->img_link,
				),
				"visibility" => array(
					"code" => "anyone"
				)
			);
		}else{
			$postParams = array(
				"content" => array(
					"title" 		=> $request->message,
					"description" 	=> $request->content_text,
					"submitted-url" => $request->link,
				),
				"visibility" => array(
					"code" => "anyone"
				)
			);
		}

		$response = $this->li->post('people/~/shares?format=json', $postParams);
		if( $response != null ){
			return response()->json(['result'=>'SUCCESS! your post in Linkedin now shared']);
		}else{
			return response()->json(['result'=>'ERROR! Linkedin share']);
		}

		/*$this->li = new link('77bxo3m22s83c2', 'POVE4Giqvd4DlTnU');
		$this->li->setAccessToken($request->token_soc);
		$options = array('json'=>
			                 array(
				                 'comment' => 'Im testing Happyr LinkedIn client! https://github.com/Happyr/LinkedIn-API-client',
				                 'visibility' => array(
					                 'code' => 'anyone'
				                 )
			                 )
		);

		$result = $this->li->post('v1/people/~/shares', $options);
		dd( $this->li );*/
	}

	public function reddit(Request $request)
	{
		$title = $request->message;
		$link = $request->link;
		$subreddit = $request->subreddits;
		$urlSubmit = "https://oauth.reddit.com/api/submit";
		//data checks and pre-setup
		if ($title == null || $subreddit == null){ return null; }
		$kind = ($link == null) ? "self" : "link";
		$postData = sprintf("kind=%s&sr=%s&title=%s&r=%s",
			$kind,
			$subreddit,
			urlencode($title),
			$subreddit);
		//if link was present, add to POST data
		if ($link != null){ $postData .= "&url=" . urlencode($link); }
		$postVals = $postData;
		$url = $urlSubmit;
		$ch = curl_init($url);
		$options = array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => "",
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 30,
		);
		if (!empty($_SERVER['HTTP_USER_AGENT'])){
			$options[CURLOPT_USERAGENT] = $_SERVER['HTTP_USER_AGENT'];
		}
		if ($postVals != null){
			$options[CURLOPT_POSTFIELDS] = $postVals;
			$options[CURLOPT_CUSTOMREQUEST] = "POST";
		}
		$headers = array("Authorization: bearer {$request->token_soc}");
		$options[CURLOPT_HEADER] = false;
		$options[CURLINFO_HEADER_OUT] = false;
		$options[CURLOPT_HTTPHEADER] = $headers;
		curl_setopt_array($ch, $options);
		$apiResponse = curl_exec($ch);
		$response = json_decode($apiResponse);
		//check if non-valid JSON is returned
		if ($error = json_last_error()){
			$response = $apiResponse;
		}
		curl_close($ch);
		if( isset($response->success) && $response->success == false ) return response()->json(['result'=>'ERROR!']);
		else return response()->json(['result'=>'SUCCESS! your post in Reddit now shared']);
	}

	public function get_subreddits(Request $request)
	{
		$where = "subscriber"; $limit = 25; $after = null; $before = null;
		$qAfter = (!empty($after)) ? "&after=".$after : "";
		$qBefore = (!empty($before)) ? "&before=".$before : "";

		/*$urlSubRel = sprintf("https://oauth.reddit.com/subreddits/mine/$where?limit=%s%s%s",
			$where,
			$limit,
			$qAfter,
			$qBefore);*/

		$urlSubRel  ="https://oauth.reddit.com/subreddits/mine/subscriber";

		$ch = curl_init($urlSubRel);
		$options = array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_CONNECTTIMEOUT => 5,
			CURLOPT_TIMEOUT => 10
		);

		if (!empty($_SERVER['HTTP_USER_AGENT'])){
			$options[CURLOPT_USERAGENT] = $_SERVER['HTTP_USER_AGENT'];
		}
		$headers = array("Authorization: bearer {$request->token_soc}");
		$options[CURLOPT_HEADER] = false;
		$options[CURLINFO_HEADER_OUT] = false;
		$options[CURLOPT_HTTPHEADER] = $headers;

		curl_setopt_array($ch, $options);
		$apiResponse = curl_exec($ch);

		$response = json_decode($apiResponse);
		//check if non-valid JSON is returned
		if ($error = json_last_error()){
			$response = $apiResponse;
		}
		curl_close($ch);
		return response(['result'=>$response->data->children]);
	}

	public function get_subreddits_web($token)
	{
		$where = "subscriber"; $limit = 25; $after = null; $before = null;
		$qAfter = (!empty($after)) ? "&after=".$after : "";
		$qBefore = (!empty($before)) ? "&before=".$before : "";

		$urlSubRel  ="https://oauth.reddit.com/subreddits/mine/subscriber";

		$ch = curl_init($urlSubRel);
		$options = array(
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_CONNECTTIMEOUT => 5,
			CURLOPT_TIMEOUT => 10
		);

		if (!empty($_SERVER['HTTP_USER_AGENT'])){
			$options[CURLOPT_USERAGENT] = $_SERVER['HTTP_USER_AGENT'];
		}
		$headers = array("Authorization: bearer {$token}");
		$options[CURLOPT_HEADER] = false;
		$options[CURLINFO_HEADER_OUT] = false;
		$options[CURLOPT_HTTPHEADER] = $headers;

		curl_setopt_array($ch, $options);
		$apiResponse = curl_exec($ch);

		$response = json_decode($apiResponse);
		//check if non-valid JSON is returned
		if ($error = json_last_error()){
			$response = $apiResponse;
		}
		curl_close($ch);
		return $response;
	}

	public function pinterest(Request $request)
	{
		$client = new Buzz;
		$auth = Pin::onlyAccessToken($client, $request->token_soc);
		$api = new API($auth);
		$note = $request->message;
		$optionalLink = $request->link;
		// Load an image from a url.
		$image = pinIMG::url('http://lorempixel.com/g/400/200/cats/');
		if( $request->img_upload_link != null ){
			$pathToFile = str_replace('https://', 'http://', $request->img_upload_link );
		}else{
			$pathToFile = str_replace('https://', 'http://', $request->img_link );
		}
		$image = pinIMG::file($pathToFile);
		$data = file_get_contents($pathToFile);
		$base64 = base64_encode($data);
		$image = pinIMG::base64($base64);
		$response = $api->createPin($request->boards, $note, $image, $optionalLink);
		if ($response->ok()) {
			$pin = $response->result(); // $pin instanceof Objects\Pin
			if($pin != null){
				return response()->json(['result'=>'SUCCESS! your post in Pinterest now shared']);
			}else{
				return response()->json(['result'=>'ERROR! Pinterest share']);
			}
		}
	}

	public function get_boards(Request $request)
	{

		$client = new Buzz;
		$auth = Pin::onlyAccessToken($client, $request->token_soc);
		$api = new API($auth);
		$response = $api->getUserBoards();
		if ($response->ok()) {
			$pagedList = $response->result(); // $pagedList instanceof Objects\PagedList
			$boards = $pagedList->items(); // array of Objects\Board objects
		}else{
			$boards = null;
		}
		return response()->json(['result'=>$boards]);
	}

	public function get_boards_web($token)
	{
		$client = new Buzz;
		$auth = Pin::onlyAccessToken($client, $token);
		$api = new API($auth);
		$response = $api->getUserBoards();
		if ($response->ok()) {
			$pagedList = $response->result(); // $pagedList instanceof Objects\PagedList
			$boards = $pagedList->items(); // array of Objects\Board objects
		}else{
			$boards = null;
		}
		return $boards;
	}

	public function instagram(Request $request) // not working
	{
		if( $request->img_upload_link != null ){
			$pathToFile = str_replace('https://', 'http://', $request->img_upload_link );
		}else{
			$pathToFile = str_replace('https://', 'http://', $request->img_link );
		}
//dd($pathToFile);
		$obj = new InstagramUpload();
		$obj->Login("yesemyes517715", "SONYvaio517715!$&@");
		//dd($request->img_link);
		$obj->UploadPhoto("185dcc2a3eef033549786d4b3aa4816b.jpeg", $request->message);
		//$obj->UploadVideo("test-video.mp4", "square-thumb.jpg", "Test Upload Video From PHP");

		if(isset($obj->upload_id)&&$obj->upload_id!=null){
			return response()->json(['result'=>'SUCCESS! your post in Instagram now shared']);
		}else{
			return response()->json(['result'=>'ERROR! Instagram share']);
		}
	}

	public function google(Request $request) // not working in public
	{
		if( $request->token_soc )
		{
			$url = 'https://www.googleapis.com/plusDomains/v1/people/' . $request->provUserId . '/activities';
			$headers = array(
				'Authorization : Bearer ' . $request->token_soc,
				'Content-Type : application/json',

			);
			if( $request->img_upload_link != null ){
				$pathToFile = str_replace('https://', 'http://', $request->img_upload_link );
				$post_data = array("object" => array('originalContent'=> $request->message,'attachments'=>[
					'image'=> array('url'=>$pathToFile),
					'url'=> $request->link,
					'objectType'=>'article']),
				                   "access" => array("items" => array(array("type" => "domain")),
				                                     "domainRestricted" => true));

			}elseif( $request->img_link != null ){
				$pathToFile = str_replace('https://', 'http://', $request->img_link );
				$post_data = array("object" => array('originalContent'=> $request->message,'attachments'=>[
																				'image'=> array('url'=>$pathToFile),
					                                             'url'=> $request->link,
																				'objectType'=>'article']),
																		"access" => array("items" => array(array("type" => "domain")),
																		"domainRestricted" => true));
			}else{
				$post_data = array("object" => array("originalContent" => $request->message),
				                   "access" => array("items" => array(array("type" => "public")), "domainRestricted" => true));
			}

			$data_string = json_encode($post_data);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_URL, $url);
			$file_result = curl_exec($ch);
			curl_close($ch);
			//dd($file_result);
			if(isset($file_result)&&$file_result!=null){
				return response()->json(['result'=>'SUCCESS! your post in Google Plus now shared']);
			}else{
				return response()->json(['result'=>'ERROR! Google Plus share']);
			}
		}
	}
}
