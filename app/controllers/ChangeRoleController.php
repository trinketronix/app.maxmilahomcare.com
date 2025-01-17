<?php

//use Phalcon\Http\Response;
//use Phalcon\Http\Client\Request;

namespace Homecare\Controllers;

use Exception;
use Phalcon\Http\Response;
use Phalcon\Http\Request;
use Phalcon\Mvc\Controller;
use Homecare\Utils\HttpRequest;

class ChangeroleController extends BaseController
{
    public function indexAction()
    {

        $username=$this->session->get('username');
        $role = 1;
        //Headers array
        $token=$this->session->get('auth-token');
        $headers=["Authorization" =>$token];
       
        $getUserResponse = HttpRequest::get('/caregivers', [
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
                "username" =>$username, 
                "role"=>$role
     
            ],
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES
        );
       
        $activateRequest = HttpRequest::put('/change-role', $jsonBody, $headers);
        //file_put_contents('response_log.txt', print_r($activateRequest, true));

        if (empty($activateRequest['data'])) {
            $this->flashSession->error($activateRequest['message']);
        } //else  $this->flashSession->{'Sucess!!'}; 


    } catch (Exception $e) {
        // Handle errors in the API requests
        $this->flashSession->error('An error occurred during the role changing:  ' . $e->getMessage());
    }
}
}        
}    