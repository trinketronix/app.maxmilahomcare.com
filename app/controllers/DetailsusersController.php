<?php

namespace App\Controllers;
use App\Utils\Endpoint;
use App\Utils\HttpRequest;
use Phalcon\Mvc\Controller;
class DetailsusersController extends BaseController {
    public function indexAction(){
        $managers = [
            'username' => 'error'
        ];
        $token = 'error';
        $token = $this->session->get('auth-token');
        $headers=["Authorization" =>$token];
        $token=$this->session->get('auth-token');
       // $id=$this->getUserId($token);
       $id = isset($_COOKIE['activateid']) ? $_COOKIE['activateid'] : null;
        $headers=["Authorization" =>$token];
        if($token != null) {
            // Test Http Request Get managers
            $response = HttpRequest::get(Endpoint::ACCOUNT, $headers, ['id' => $id]);
            $accounts = $response['data'];
            $this->view->setVars([
                'id' => $id,
                'managers' => $accounts,

            ]);

            $this->session->set('auth-token', $token);
            $role=$this->getRole($token);
            // Redirect to the 'main' page
            if (null != $token)
                if ( $role<2)
                    $liga='main';
                else
                    $liga='caregiver';

        }
        //$this->view->setVar("managers", $accounts);
        //$this->view->setVar("myid", $id);
        //$this->view->setVar("liga",$liga);

    }
}