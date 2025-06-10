<?php

//use Phalcon\Http\Response;
//use Phalcon\Http\Client\Request;

namespace App\Controllers;

use Exception;
use Phalcon\Http\Response;
use Phalcon\Http\Request;
use Phalcon\Mvc\Controller;
use App\Utils\HttpRequest;


class VisitController extends BaseController
{
    public function indexAction()
    {

        $uid = $_GET['hiddenField']; 

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
            $start_time = $this->request->getPost('start_time');
            $end_time = $this->request->getPost('end_time');
            $note = $this->request->getPost('note');
            $uid= 1;  
           
$jsonBody = json_encode(
    [
       
        "patient_id" =>$uid,
        "start_time" =>$start_time,
        "end_time" =>$end_time,
        "note" =>$note
        
    ],
    JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES
);
//file_put_contents('response3_log2.txt', print_r($jsonBody, true));
        $token=$this->session->get('auth-token');
        $headers=["Authorization" =>$token];
       
try {
    $signupRequest = HttpRequest::post(Endpoint::VISIT, $jsonBody,$headers);

     //file_put_contents('response_log.txt', print_r($jsonBody, true));   
     //file_put_contents('response_log2.txt', print_r($signupRequest, true));
    $this->flashSession->error($signupRequest['message']);
   // }
    $data = $signupRequest['data'];
    // Redirect to the 'main' page
    $this->flashSession->success('Visit registered successfully');
    //$this->response->redirect('main');

} catch (Exception $e) {
    // Handle errors in the API requests
    $this->flashSession->error('An error occurred during the visit registration: ' . $e->getMessage());
    //file_put_contents('response_log2.txt', print_r('errorzaso', true));
}

}
    }
}
