<?php
function mobile_user_agent_switch()
{
	$device = '';

	if( stristr($_SERVER['HTTP_USER_AGENT'],'ipad') ) {
		$device = "iphone";
	} else if( stristr($_SERVER['HTTP_USER_AGENT'],'iphone') || strstr($_SERVER['HTTP_USER_AGENT'],'iphone') ) {
		$device = "iphone";
	} else if( stristr($_SERVER['HTTP_USER_AGENT'],'blackberry') ) {
		$device = "blackberry";
	} else if( stristr($_SERVER['HTTP_USER_AGENT'],'android') ) {
		$device = "android";
	}

	if( $device ) {
		return $device;
	} return false; {
		return false;
	}
}
?>