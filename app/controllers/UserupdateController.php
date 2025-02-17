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

//inicio
$managers = [
    'username' => 'error'
];
$token = 'error';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the manager ID from the form submission
    $uid = $_POST['hiddenField'];}
$token = $this->session->get('auth-token');
$headers=["Authorization" =>$token];

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
    $signupRequest = HttpRequest::put('/user/'.$uid, $jsonBody,$headers);
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
