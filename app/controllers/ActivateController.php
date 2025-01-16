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

        $username=$this->session->get('username');
        
        //Headers array
        $token=$this->session->get('auth-token');
        $headers=["Authorization" =>$token];
       
        $getUserResponse = HttpRequest::get('/not-activated', [
                'Content-Type' => 'application/json',
                'Authorization' => $token
            ]);
            $users = $getUserResponse['data'];

       
        $this->view->setVar("users", $users);
    //    file_put_contents('response4_log.txt', print_r($users, true));




if ($this->request->isPost()) {
    $username = $this->request->getPost('username');

    
    try {

        // Prepare the JSON body for the login request
        $jsonBody = json_encode(
            [
                "username" =>$username 
       
     
            ],
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES
        );
       
        $activateRequest = HttpRequest::put('/activate', $jsonBody, $headers);
        //file_put_contents('response_log.txt', print_r($jsonBody, true));

        if (empty($activateRequest['data'])) {
            $this->flashSession->error($activateRequest['message']);
        } //else  $this->flashSession->{'Sucess!!'}; 


    } catch (Exception $e) {
        // Handle errors in the API requests
        $this->flashSession->error('An error occurred during the activation: ' . $e->getMessage());
    }
}
}    
}    