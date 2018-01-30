<?php


/* Require library */
require_once 'class.googleplus.php';
/* Configuration Values */
$config['consumer_key'] = '114777295493-rlp2c28pr2l2dpmpi4spec663fjrf5si.apps.googleusercontent.com';
$config['consumer_secret']   = 'iUIqdh9Hf9-Tbm8iOVLV3x3L';
$config['callbackUrl']  = 'https://' . $_SERVER['SERVER_NAME'] . '/aa?verify';

$GooglePlus = new GooglePlusPHP($config);

/* Verification phase */
if (!isset($_SESSION['googlePlusOAuth']) && isset($_GET['verify']) && isset($_GET['code']))
{
	die( $_GET['code'] );
	try {
		unset($_SESSION['googlePlusOAuth']);
		$accessToken = $GooglePlus->getAccessToken($_GET['code']);
		$GooglePlus->setOAuthToken($accessToken->access_token, false);
		$_SESSION['googlePlusOAuth'] = $accessToken;
	} catch (Exception $e) {
		die($e->getMessage());
		exit;
	}
	header('Location: index.php');
	exit;
}

/* No token, and no ?verify . Redirect to auth. */
if (!isset($_SESSION['googlePlusOAuth'])){
	header('Location: ' . $GooglePlus->getAuthorizationUrl() );
}

/* Set Access Token */
$GooglePlus->setOAuthToken($_SESSION['googlePlusOAuth']['access_token']);

if (!$GooglePlus->testAuth())
	die('Your token probably expired, or was not valid. Clear the session and try again.');
	

/* Profile */
$profile = $GooglePlus->getMyProfile();


/* My Activities */
$activities = $GooglePlus->getMyActivities();

/* People Search */
if (isset($_GET['search'])){
	if (isset($_GET['search_pagetoken'])):
		$search_pagetoken = $_GET['search_pagetoken'];
	else:
		$search_pagetoken = null;
	endif;
	$search_results = $GooglePlus->searchPeople($_GET['search'], $search_pagetoken);
}

/* Load Profile, override $activities */
if (isset($_GET['profile_id'])){
	$profile_id = $_GET['profile_id'];
	if (!is_numeric($profile_id)) continue;
	
	$activities = $GooglePlus->getPublicActivities($profile_id);
	
	$user_profile = $GooglePlus->getUserProfile($profile_id);
}

?>
<html>
<head>
<title>Sample Google+</title>
</head>
<body>

<h1>Google+ Example Script</h1>

<h2>$profile</h2>
<pre><?php var_dump($profile); ?></pre>
<hr />

<h2>$search_results</h2>
<p>Set $_GET['search'] to view results.</p>
<pre><?php var_dump($search_results); ?></pre>
<hr />

<h2>$user_profile</h2>
<p>Set $_GET['profile_id'] to view results.</p>
<pre><?php var_dump($user_profile); ?></pre>
<hr />

<h2>$activities</h2>
<pre><?php var_dump($activities); ?></pre>

</body>
</html>