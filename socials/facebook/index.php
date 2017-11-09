<?php
	require "main.php";
	 
	if (isset($_SESSION['token'])) {
		
	  	$linkData = [
		  'link' => 'http://bavarianautoparts.net/',
		  'message' => 'User provided message',
		];

	    try {
		  // Returns a `Facebook\FacebookResponse` object
		  $response = $fb->post('/me/feed', $linkData, $_SESSION['token']);
		} catch(Facebook\Exceptions\FacebookResponseException $e) {
		  echo 'Graph returned an error: ' . $e->getMessage();
		  exit;
		} catch(Facebook\Exceptions\FacebookSDKException $e) {
		  echo 'Facebook SDK returned an error: ' . $e->getMessage();
		  exit;
		}

		$graphNode = $response->getGraphNode();

		echo 'Posted with id: ' . $graphNode['id'];
	}
	else{
		$helper = $fb->getRedirectLoginHelper();
		$permissions = ['email', 'user_posts', 'manage_pages', 'publish_actions', 'publish_pages'];
		$callback    = 'http://packetcode.com/apps/srivathsan/pagepost/app.php';
		$loginUrl    = $helper->getLoginUrl($callback, $permissions);
		echo '<a href="' . $loginUrl . '">Log in with Facebook!</a>';
	}
?>