<?php

session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);
$redirect_uri = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
//require_once 'vendor/autoload.php';
   require_once( '../../socials/google/vendor/autoload.php' );
use Aws\S3\S3Client;
$client = new Google_Client();
$client->setClientId("24418481962-52f7vpeofhhpdlgl16rmefuhudionjj2.apps.googleusercontent.com");
$client->setClientSecret("tfSbrFKlJ1pvaAAohNrfm4La");
$client->setRedirectUri($redirect_uri);
$client->setAccessType("online");
//$client->setdeveloperkey('AIzaSyC15oUAZ67EnHvRiG51kUb97kriY1iiOl0');
$client->setScopes(array(

    'https://www.googleapis.com/auth/plus.me',
    'https://www.googleapis.com/auth/plus.stream.read',
    'https://www.googleapis.com/auth/plus.stream.write',
));
if (isset($_REQUEST['logout'])) {
    unset($_SESSION['upload_token']);
}
if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    
    $client->setAccessToken($token);
// store in the session also
    $_SESSION['upload_token'] = $token;
// redirect back to the example
    header('Location: ' . filter_var($redirect_uri, FILTER_SANITIZE_URL));
}
if (!empty($_SESSION['upload_token'])) {
    $client->setAccessToken($_SESSION['upload_token']);
    if ($client->isAccessTokenExpired()) {
        unset($_SESSION['upload_token']);
    }
} else {
    $authUrl = $client->createAuthUrl();
}
if ($client->getAccessToken()) {
    $t = $client->getAccessToken();
    if ($client->isAccessTokenExpired()) {
        $client->refreshToken($t['access_token']);
    }
##########################################################
    $http = $client->authorize();
    $plus = new \Google_Service_Plus($client);
    $plusdomains = new Google_Service_PlusDomains($client);
    
    $result = $plus->people->get('me');
    $user_id = $result->id;
    $accesstoken = $t['access_token'];
    
    
    $url = 'https://www.googleapis.com/plusDomains/v1/people/' . $user_id . '/activities';
    $headers = array(
        'Authorization : Bearer ' . $accesstoken,
        'Content-Type : application/json',
        
    );
   
   
    $post_data = array("object" => array("content" => "Verchapessss"), "access" => array("items" => array(array("type" => "domain")),"domainRestricted" => true));
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

    print_r($file_result);
    exit;
}
?>
<div class="box">
<?php if (isset($authUrl)): ?>
        <div class="request">
            <a class='login' href='<?= $authUrl ?>'>Connect Me!</a>
        </div>


<?php endif ?>
</div>