<?php
session_start();
require "LinkedIn/LinkedIn.php";
use LinkedIn\LinkedIn;

$li = new LinkedIn(
  array(
    'api_key' => '86kbroppel9fqk',
    'api_secret' => 'Ks0UKEgWWRW5xPRS',
    'callback_url' => 'http://localhost/linkedin/index.php'
  )
);

$url = $li->getLoginUrl(
  array(
    LinkedIn::SCOPE_BASIC_PROFILE,
    LinkedIn::SCOPE_EMAIL_ADDRESS, 
    LinkedIn::SCOPE_WRITE_SHARE,
    LinkedIn::SCOPE_READ_WRITE_COMPANY_ADMIN,
  )
);

echo "<a href='$url'>link</a>";


if( isset($_SESSION['token']) && $_SESSION['token'] != null )
{
  $token = $_SESSION['token'];
  $li->setAccessToken($token);

  $postParams = array(
        "content" => array(
            "title" => "Test share api",
            "description" => "documentation wrong :)",
            "submitted-url" => "http://phplucidframe.sithukyaw.com"
        ),
        "visibility" => array(
            "code" => "anyone"
        )
  );

  $result = $li->post('/people/~/shares?format=json', $postParams);

  var_dump($result);
}

  

if( isset($_REQUEST['code']) )
{
	$token = $li->getAccessToken($_REQUEST['code']);
  $token_expires = $li->getAccessTokenExpiration();
	
  $_SESSION['token'] = $token;
}