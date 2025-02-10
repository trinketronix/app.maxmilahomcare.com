<?php

namespace Homecare\Controllers;
use Homecare\Utils\HttpRequest;
use Phalcon\Mvc\Controller;
class UsersController extends BaseController {
    public function indexAction(){
        $managers = [
            'username' => 'error'
        ];
        $token = 'error';
        $token = $this->session->get('auth-token');
        $headers=["Authorization" =>$token];
        if($token != null) {
            // Test Http Request Get managers
            $getManaResponse = HttpRequest::get('/accounts',$headers);
            //file_put_contents('response_log.txt', print_r($getManaResponse, true));  
            //file_put_contents('response_log.txt', print_r($getManaResponse, true));  
            $managersjson = $getManaResponse['data'];
            //file_put_contents('response2_log.txt', print_r($managersjson, true));  
            $array = $getManaResponse['data'];
            $accounts = $getManaResponse['data']['accounts'];
            //file_put_contents('response_log.txt', print_r($getManaResponse, true));  
            //file_put_contents('response3_log.txt', print_r($accounts , true));  

        }
        $this->view->setVar("managers", $accounts);

    }
}