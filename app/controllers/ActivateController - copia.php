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

      
        $username= $this->session->get('username');
        
       // $role = $this->session->get('role');;
        //Headers array
        $token=$this->session->get('auth-token');
        $role=$this->getRole($token);
        $headers=["Authorization" =>$token];
       
//        $getUserResponse = HttpRequest::get('/caregivers', [
//                'Content-Type' => 'application/json',
//                'Authorization' => $token
//            ]);

      $managers = [
            'username' => 'error'
        ];
        $token = 'error';
        $token = $this->session->get('auth-token');
        $headers=["Authorization" =>$token];
        if($token != null) {
            // Test Http Request Get managers
            $getManaResponse = HttpRequest::get('/accounts',$headers);
            //file_put_contents('response_log.txt', print_r($getManaResponse, true));  
            //file_put_contents('response_log.txt', print_r($getManaResponse, true));  
            $managersjson = $getManaResponse['data'];
            //file_put_contents('response2_log.txt', print_r($managersjson, true));  
            $array = $getManaResponse['data'];
            $accounts = $getManaResponse['data']['accounts'];
            //file_put_contents('response_log.txt', print_r($getManaResponse, true));  
            //file_put_contents('response3_log.txt', print_r($accounts , true));  

        }
        $this->view->setVar("users", $accounts);
        
        //file_put_contents('response4_log.txt', print_r($accounts, true));



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
}
}    
}    