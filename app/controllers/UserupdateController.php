<?php

//use Phalcon\Http\Response;
//use Phalcon\Http\Client\Request;


namespace Homecare\Controllers;

use Exception;
use Phalcon\Http\Response;
use Phalcon\Http\Request;
use Phalcon\Mvc\Controller;
use Homecare\Utils\HttpRequest;


class UserupdateController extends BaseController
{
    public function indexAction()
    {

//inicio
$managers = [
    'username' => 'error'
];

$token = 'error';

$token = $this->session->get('auth-token');
//if($token != null) {
    
//    $getManaResponse = HttpRequest::get('/users', [
//        'Content-Type' => 'application/json',
//        'Authorization' => $token
//    ]);
//    $managers = $getManaResponse['data'];

 
//}

//$this->view->setVar("managers", $managers);

//fin
        if ($this->request->isPost()) {
            $lastname = $this->request->getPost('lastname');
            $firstname = $this->request->getPost('firstname');
            $middlename = $this->request->getPost('middlename');
            $birthdate = $this->request->getPost('birthdate');
            $service_provider_code = $this->request->getPost('service_provider_code');
            $address = $this->request->getPost('address');
            $city = $this->request->getPost('city');
            $languages = $this->request->getPost('languages');
            $user= 2;  
           
$jsonBody = json_encode(
    [
       
        "lastname" =>$lastname,
        "firstname" =>$firstname,
        "middlename" =>$middlename,
        "birthdate" =>$birthdate,
        "service_provider_code"=> $service_provider_code,
        "address" => $address,
        "city" => $city,
        "languages" => $languages
    ],
    JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES
);
file_put_contents('response3_log2.txt', print_r($jsonBody, true));
        $token=$this->session->get('auth-token');
        $headers=["Authorization" =>$token];
       
try {
    $signupRequest = HttpRequest::put('/user/'.$user, $jsonBody,$headers);

     file_put_contents('response_log.txt', print_r($jsonBody, true));   
     file_put_contents('response_log2.txt', print_r($signupRequest, true));
    $this->flashSession->error($signupRequest['message']);
   // }
    $data = $signupRequest['data'];
    // Redirect to the 'main' page
    $this->flashSession->success('User updated successfully');
    //$this->response->redirect('main');

} catch (Exception $e) {
    // Handle errors in the API requests
    $this->flashSession->error('An error occurred during update: ' . $e->getMessage());
    file_put_contents('response_log2.txt', print_r('errorzaso', true));
}

}
    }
}
