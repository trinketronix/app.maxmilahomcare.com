<?php

namespace Homecare\Controllers;

use Homecare\Utils\HttpRequest;
use Phalcon\Mvc\Controller;

class TestController extends Controller {
    public function indexAction(){

        $response = HttpRequest::get('/managers',[
            'Content-Type' => 'application/json',
            'Authorization' => 'eyJpZCI6MSwidXNlcm5hbWUiOiJ0b2Rkc2FscGVuQGdtYWlsLmNvbSIsInJvbGUiOjAsImV4cGlyYXRpb24iOjE3MzU4ODQzMzkxMzJ9'
        ]);
        $managers = $response['data'];
        $this->view->setVar("managers",$managers);
    }
}