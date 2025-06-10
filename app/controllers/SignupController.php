<?php

//use Phalcon\Http\Response;
//use Phalcon\Http\Client\Request;


namespace App\Controllers;

use Exception;
use Phalcon\Http\Response;
use Phalcon\Http\Request;
use Phalcon\Mvc\Controller;
use App\Utils\HttpRequest;


class SignupController extends BaseController {
    public function indexAction() {
        if ($this->request->isPost()) {
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');
       

$jsonBody = json_encode(
    [
        "username" => $username,
        "password" => $password
    ],
    JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES
);

try {
    $signupRequest = HttpRequest::post(Endpoint::REGISTER, $jsonBody);
    if (empty($signupRequest['data'])) {
        $this->flashSession->error($signupRequest['message']);
    }
    $data = $signupRequest['data'];
    // Redirect to the 'main' page
    $this->flashSession->success('User created successfully');
    //$this->response->redirect('main');

} catch (Exception $e) {
    // Handle errors in the API requests
    $this->flashSession->error('An error occurred during signup: ' . $e->getMessage());
}

}
    }
}
