<?php

//use Phalcon\Http\Response;
//use Phalcon\Http\Client\Request;

namespace App\Controllers;

use Exception;
use App\Utils\Endpoint;
use Phalcon\Http\Response;
use Phalcon\Http\Request;
use Phalcon\Mvc\Controller;
use App\Utils\HttpRequest;



class ChangeroleController extends BaseController
{
    public function indexAction()
    {

       
    

if ($this->request->isPost()) {
    $username = $this->request->getPost('username');
    $newid = $this->request->getPost('id');
    $role=1;
    //file_put_contents('response_log5.txt', print_r($username, true));
    $token=$this->session->get('auth-token');
    $role=$this->getRole($token);
    $headers=["Authorization" =>$token];
    $username = $this->request->getPost('hiddenusername');
    //file_put_contents('resp.txt', print_r($username, true));
//if ($this->request->isPost()) {
//    $username = $this->request->getPost('username');
//$username = isset($_COOKIE['username']) ? $_COOKIE['username'] : null;
//$message = "wrong answer";
//echo "<script type='text/javascript'>alert('$username');</script>";   

    try {

        // Prepare the JSON body for the login request
        $jsonBody = json_encode(
            [
                "username" =>$username, 
                "role"=>$role
     
            ],
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES
        );
        $response = HttpRequest::get(Endpoint::CHANGE_ROLE, $headers, ['id' => $id]);
        $response = HttpRequest::put('/change-role', $jsonBody, $headers);
        //file_put_contents('responsi_log.txt', print_r($username, true));
        $this->flashSession->success('Role Changed');
        if (empty($activateRequest['data'])) {
            $this->flashSession->error($activateRequest['message']);
        } //else  $this->flashSession->{'Sucess!!'}; 


    } catch (Exception $e) {
        // Handle errors in the API requests
        $this->flashSession->error('An error occurred during the role changing process:  ' . $e->getMessage());
        //file_put_contents('responsi2_log.txt', print_r($username, true));

    }
}
}        
}    