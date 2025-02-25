<?php
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
//inicio modificación
//public function indexAction()
//{
    // Assuming managerId is set from a cookie
    $managerId = $_COOKIE['activateid'];

    // API call to fetch user data based on the managerId
    $token = $this->session->get('auth-token');
    $headers = ["Authorization" => $token];

    try {
        //HttpRequest::get('/accounts',$headers);
        // Make API call to fetch the user details
        $getUserResponse = HttpRequest::get('/user/2', $headers);
        if (isset($getUserResponse['data'])) {
            // Pass the user data to the Volt template
            $this->view->user = $getUserResponse['data'];
        } else {
            $this->flashSession->error('Unable to fetch user data');
        }
    } catch (Exception $e) {
        $this->flashSession->error('Error fetching user data: ' . $e->getMessage());
    }
//}
$this->view->setVar("user", $getUserResponse['data']);

//fin modificacion
        //inicio
$managers = [
    'username' => 'error'
];
$token = 'error';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the manager ID from the form submission
    $uid = $_GET['hiddenField'];}
     
        if (isset($_COOKIE['activateid'])) {
            $uid = $_COOKIE['activateid'];
//        $managerId=2;
        }
        $uid = $this->request->getPost('zipcode');
        $uid = $this->request->getPost('hiddenField');
$token = $this->session->get('auth-token');
$headers=["Authorization" =>$token];
$cadena='/user/'.$uid;
        if ($this->request->isPost()) {
            $lastname = $this->request->getPost('lastname');
            $firstname = $this->request->getPost('firstname');
            $middlename = $this->request->getPost('middlename');
            $birthdate = $this->request->getPost('birthdate');
            $service_provider_code = $this->request->getPost('service_provider_code');
            $address = $this->request->getPost('address');
            $city = $this->request->getPost('city');
            $languages = $this->request->getPost('languages');
           
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
//file_put_contents('response3_log2.txt', print_r($jsonBody, true));
        $token=$this->session->get('auth-token');
        $headers=["Authorization" =>$token];
       
try {
   // $getManaResponse = HttpRequest::get('/accounts',$headers);
   //$getUserResponse = HttpRequest::get('/user/2', $headers); 
  // $signupRequest = HttpRequest::put('/user/2', $jsonBody,$headers);
  $signupRequest = HttpRequest::put($cadena, $jsonBody,$headers);
  file_put_contents('response_log.txt', print_r($signupRequest, true));  
  file_put_contents('response_log2.txt', print_r($cadena, true));
  file_put_contents('response_log3.txt', print_r($jsonBody, true));
  file_put_contents('response_log4.txt', print_r($headers, true));

            //file_put_contents('response_log.txt', print_r($getManaResponse, true));  
            
  $this->flashSession->error($signupRequest['message']);
   // }
    $data = $signupRequest['data'];
    // Redirect to the 'main' page
    $this->flashSession->success('User updated successfully');
    

} catch (Exception $e) {
    // Handle errors in the API requests
    $this->flashSession->error('An error occurred during update: ' . $e->getMessage());
}

}
    }
}
