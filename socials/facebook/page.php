<?php
    
    require "main.php";

    if(isset($_SESSION['token'])){
        
        $id = $_POST['pageid'];
        $message = $_POST['message'];
        
        $data = array(
                'message' => $message
        );
        
        $res = $fb->get('/me/accounts', $_SESSION['token']);
        $res = $res->getDecodedBody();
        
        foreach($res['data'] as $page){
            if($page['id'] == $id){
                $accesstoken = $page['access_token'];
            }
        }
        
        $res = $fb->post($id . '/feed/', $data, $accesstoken);
        header('Location: index.php');
        
    }


?>