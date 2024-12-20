<?php

use Phalcon\Http\Response;
use Phalcon\Http\Client\Request;

class ForgotController extends ControllerBase
{
    public function indexAction()
    {
//        if ($this->request->isPost()) {
//            $username = $this->request->getPost('username');
//            $newPassword = $this->request->getPost('new_password');
//
//            try {
//                $request = new Request();
//                $request->setBaseUri('https://api.maximus.io');
//                $request->setHeader('Content-Type', 'application/json');
//                $request->setJsonData([
//                    'username' => $username,
//                    'new_password' => $newPassword
//                ]);
//
//                $response = $request->post('/change-password');
//                $result = json_decode($response->getBody());
//
//                if ($result->success) {
//                    $this->flash->success('Password changed successfully');
//                    return $this->response->redirect('login');
//                }
//            } catch (\Exception $e) {
//                $this->flash->error('Error changing password');
//            }
//        }
    }
} 