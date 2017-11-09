<?php
	require "fbsdk/src/Facebook/autoload.php";
	session_start();
	$fb = new Facebook\Facebook([
	  'app_id'                => '',
	  'app_secret'            => '',
	  'default_graph_version' => 'v2.5',
	]);
?>