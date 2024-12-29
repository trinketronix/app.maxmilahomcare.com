<?php

namespace Homecare\Controllers;

use Homecare\Utils\HttpRequest;
use Phalcon\Mvc\Controller;

class TestController extends Controller {
    public function indexAction(){

        $jsonBody = json_encode(
            [
                "username" => "toddsalpen@gmail.com",
                "password" => "Mju72wsx@#$&"
            ],
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES
        );

        $post = HttpRequest::post('/login', $jsonBody);

//        $postStatus = $postTestResponse['status'];
//        $postCode = $postTestResponse['code'];
//        $postMessage = $postTestResponse['message'];
//        $postData = $postTestResponse['data'];
//
//        $token = $postData['token'];

        $this->view->setVar("post", $post);

//        if($token != null) {
//            // TEST GET METHOD
//            $getTestResponse = HttpRequest::get('/managers', [
//                'Content-Type' => 'application/json',
//                'Authorization' => $token
//            ]);
//            $managers = $getTestResponse['data'];
//        }



    }
}