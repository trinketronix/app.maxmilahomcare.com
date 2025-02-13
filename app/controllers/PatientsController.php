<?php

//use Phalcon\Http\Response;
//use Phalcon\Http\Client\Request;


namespace Homecare\Controllers;

use Exception;
use Phalcon\Http\Response;
use Phalcon\Http\Request;
use Phalcon\Mvc\Controller;
use Homecare\Utils\HttpRequest;


class PatientsController extends BaseController
{
    public function indexAction()
    {

//inicio
$managers = [
    'username' => 'error'
];

$token = 'error';

$token = $this->session->get('auth-token');
if($token != null) {
    
    $getManaResponse = HttpRequest::get('/patients', [
        'Content-Type' => 'application/json',
        'Authorization' => $token
    ]);
    $managers = $getManaResponse['data'];

 
}

$this->view->setVar("managers", $managers);

//fin

    }
}
