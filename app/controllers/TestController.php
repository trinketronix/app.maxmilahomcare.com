<?php

namespace Homecare\Controllers;

use Homecare\Utils\HttpRequest;
use Phalcon\Mvc\Controller;

class TestController extends Controller {
    public function indexAction(){
        $managers = [
            'username' => 'error'
        ];
        $token = 'error';

        $jsonBody = json_encode(
            [
                "username" => "toddsalpen@gmail.com",
                "password" => "Mju72wsx@#$&"
            ],
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES
        );

        $postTestResponse = HttpRequest::post('/login', $jsonBody);

        $postStatus = $postTestResponse['status'];
        $postCode = $postTestResponse['code'];
        $postMessage = $postTestResponse['message'];
        $postData = $postTestResponse['data'];

        $token = $postData['token'];


        if($token != null) {
            // TEST GET METHOD
            $getTestResponse = HttpRequest::get('/managers', [
                'Content-Type' => 'application/json',
                'Authorization' => $token
            ]);
            $managers = $getTestResponse['data'];
        }


        $this->view->setVar("token", $token);
        $this->view->setVar("managers", $managers);
    }
}