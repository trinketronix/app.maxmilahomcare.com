<?php

namespace Homecare\Controllers;

use Phalcon\Http\Response;
use Phalcon\Http\Request;
use Phalcon\Mvc\Controller;

class LoginController extends Controller
{
    public function indexAction()
    {
        if ($this->request->isPost()) {
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');
            
//            try {
//                $request = new Request();
//                $request->setBaseUri('https://api.maximus.io');
//                $request->setHeader('Content-Type', 'application/json');
//                $request->setJsonData([
//                    'username' => $username,
//                    'password' => $password
//                ]);
//
//                $response = $request->post('/login');
//                $result = json_decode($response->getBody());
//
//                if (isset($result->token)) {
//                    $this->session->set('auth-token', $result->token);
//                    $this->session->set('username', $username);
//                    return $this->response->redirect('main');
//                }
//            } catch (\Exception $e) {
//                $this->flash->error('Invalid credentials');
//            }
        }
    }
} 