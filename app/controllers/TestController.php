<?php

namespace Homecare\Controllers;

use Homecare\Utils\HttpRequest;
use Phalcon\Mvc\Controller;

class TestController extends BaseController {
    public function indexAction(){

        $managers = [
            'username' => 'error'
        ];

        $token = 'error';

//        $jsonBody = json_encode(
//            [
//                "username" => "toddsalpen@gmail.com",
//                "password" => "Mju72wsx@#$&"
//            ],
//            JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES
//        );

    //    $post = HttpRequest::post('/login', $jsonBody);

   //     $data = $post['data'];
     //   $token = $data['token'];
$token = $this->session->get('auth-token');
        if($token != null) {
            // Test Http Request Get managers
            $getManaResponse = HttpRequest::get('/managers', [
                'Content-Type' => 'application/json',
                'Authorization' => $token
            ]);
            $managers = $getManaResponse['data'];

            // Test Http Request Get caregivers
            $getCareResponse = HttpRequest::get('/caregivers', [
                'Content-Type' => 'application/json',
                'Authorization' => $token
            ]);
            $caregivers = $getCareResponse['data'];
        }
//$username=$token;
        $this->view->setVar("managers", $managers);
        $this->view->setVar("caregivers", $caregivers);

    }
}