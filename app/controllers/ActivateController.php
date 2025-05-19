<?php

//use Phalcon\Http\Response;
//use Phalcon\Http\Client\Request;

namespace Homecare\Controllers;

use Exception;
use Homecare\Utils\Endpoint;
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
$id = isset($_COOKIE['activateid']) ? $_COOKIE['activateid'] : null;
$headers=["Authorization" =>$token];


//echo $username;
    try {
        if($token != null) {
            // Test Http Request Get managers
            $response = HttpRequest::get(Endpoint::ACTIVATE_ACOUNT, $headers, ['id' => $id]);
            $accounts = $response['data'];
            $this->view->setVars([
                'id' => $id,
                'managers' => $accounts,
            ]);
            // file_put_contents('response_log.txt', print_r($jsonBody, true));
            $this->flashSession->success('User activated');
            if (empty($response['data'])) {
                //$this->flashSession->error($response['message']);
                echo 'Esta vacío';
            } else  $this->flashSession->{'Sucess!!'};
        }

    } catch (Exception $e) {
        // Handle errors in the API requests
        $this->flashSession->error('An error occurred during the activation: ' . $e->getMessage());
    }
    echo "<script type='text/javascript'>alert('$username');</script>";
//}
}    
}    