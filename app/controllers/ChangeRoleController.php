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



if ($this->request->isPost()) {
    $username = $this->request->getPost('username');
    $role = 1;
    
    try {

        // Prepare the JSON body for the login request
        $jsonBody = json_encode(
            [
                "username" => $username,
                "role"=>$role
     
            ],
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES
        );
        //Headers array
        $token=$this->session->get('auth-token');
        $headers=["Authorization" =>$token];

        $changeroleRequest = HttpRequest::put('/change-role', $jsonBody, $headers);
       //file_put_contents('response_log.txt', print_r($jsonBody, true));

        if (empty($changeroleRequest['data'])) {
            $this->flashSession->error($changeroleRequest['message']);
        }

        $data = $changeroleRequest['data'];
       //file_put_contents('response_log2.txt', print_r($data, true));
        // Redirect to the 'main' page
        //if ($token != null)
     //       $this->response->redirect('main');

    } catch (Exception $e) {
        // Handle errors in the API requests
        $this->flashSession->error('An error occurred during the role change: ' . $e->getMessage());
        //file_put_contents('response_log3.txt', print_r($e->getMessage(), true));
       // return $this->dispatcher->forward([
       //     'controller' => 'main',
       //     'action' => 'index'
    //    ]
   // );
    }
}
}    
}    