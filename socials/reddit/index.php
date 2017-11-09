<?php

require_once("reddit.php");
$reddit = new reddit();


$title = "axper jan inch ka?";
$link = "https://list.am";
$subreddit = "news";
$response = $reddit->createStory($title, $link, $subreddit);

//$user = $reddit->getUser();

var_dump($reddit); 