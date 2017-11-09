<?php

namespace App\Http\Controllers;
session_start();

require_once( base_path('socials/facebook/fbsdk/src/Facebook/autoload.php') );
require_once( base_path('socials/twitter/TwitterAPIExchange.php') );
require_once( base_path('socials/linkedin/LinkedIn/LinkedIn.php') );
require_once( base_path('socials/reddit/reddit.php') );
require_once( base_path('socials/pinterest/vendor/autoload.php') );
require_once( base_path('socials/instagram/instagram_post.php') );

use Facebook\Facebook;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use TwitterAPIExchange;
use LinkedIn\LinkedIn;
use reddit;
use instagram_post;
use Pinterest\Authentication as Pin;
use Pinterest\Http\BuzzClient as Buzz;
use Pinterest\App\Scope;
use Pinterest\Api as API;
use Pinterest\Image as pinIMG;

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
				'source' => $request->img_upload_link,
			];
		}
		elseif($request->img_link != null){
			$request->img_link = str_replace('https://', 'http://', $request->img_link );
			$linkData = [
				'link' => $request->link,
				'message' => $request->message,
				'source' => $request->img_link,
			];
		}
		else{
			$linkData = [
				'link' => $request->link,
				'message' => $request->message,
			];
		}

		try {
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
			return response()->json(['result'=>'success']);
		}else{
			return response()->json(['result'=>'error']);
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
				'status' => $request->message.' '.$request->link );
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


		if( $response != null ){
			return response()->json(['result'=>'success']);
		}else{
			return response()->json(['result'=>'error']);
		}
	}

	public function google(Request $request) // not working
	{
		return 'google plus API';
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
		$response = $this->li->post('/people/~/shares?format=json', $postParams);
		if( $response != null ){
			return response()->json(['result'=>'success']);
		}else{
			return response()->json(['result'=>'error']);
		}
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
		return response()->json(['result'=>$response->success]);
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
		return ($response->data->children);
	}

	public function instagram(Request $request)
	{
		$upload_image_filename = 'download.jpg'; // TODO; Link to your image from here
		$image_caption = 'My example image caption #InstagramImageAPI'; // TODO; Add your image caption here

		$ig = new instagram_post();

		if ($ig->doPostImage($upload_image_filename, $image_caption)) {
			echo "<pre>";
			var_dump($ig);
			echo "</pre>";
		} else {
			return response(['instagram'=>'error']);
		}
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
			$pathToFile = $request->img_upload_link;
		}else{
			$pathToFile = $request->img_link;
		}
		$image = pinIMG::file($pathToFile);
		$data = file_get_contents($pathToFile);
		$base64 = base64_encode($data);
		$image = pinIMG::base64($base64);
		$response = $api->createPin($request->boards, $note, $image, $optionalLink);
		if ($response->ok()) {
			$pin = $response->result(); // $pin instanceof Objects\Pin
			if($pin != null){
				return response()->json(['result'=>'success']);
			}else{
				return response()->json(['result'=>'error']);
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
		return response()->json(['result'=>$boards]);
	}

}
