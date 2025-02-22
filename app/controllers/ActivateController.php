<?php

//use Phalcon\Http\Response;
//use Phalcon\Http\Client\Request;

namespace Homecare\Controllers;

use Exception;
use Phalcon\Http\Response;
use Phalcon\Http\Request;
use Phalcon\Mvc\Controller;
use Homecare\Utils\HttpRequest;

class ActivateController extends BaseController
{
    public function indexAction()
    {

      
        //$username= $this->session->get('username');
        
        //Headers array
        $token=$this->session->get('auth-token');
        $role=$this->getRole($token);
        $headers=["Authorization" =>$token];


//if ($this->request->isPost()) {
//    $username = $this->request->getPost('username');
$username = isset($_COOKIE['username']) ? $_COOKIE['username'] : null;
//$message = "wrong answer";
//echo "<script type='text/javascript'>alert('$username');</script>";   


//echo $username;
    try {

        // Prepare the JSON body for the login request
        $jsonBody = json_encode(
            [
                "username" =>$username 
       
     
            ],
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES
        );
       
        $activateRequest = HttpRequest::put('/activate-account', $jsonBody, $headers);
       // file_put_contents('response_log.txt', print_r($jsonBody, true));
       $this->flashSession->success('User activated');
        if (empty($activateRequest['data'])) {
            $this->flashSession->error($activateRequest['message']);
        } else  $this->flashSession->{'Sucess!!'}; 


    } catch (Exception $e) {
        // Handle errors in the API requests
        $this->flashSession->error('An error occurred during the activation: ' . $e->getMessage());
    }
    echo "<script type='text/javascript'>alert('$username');</script>";
//}
}    
}    