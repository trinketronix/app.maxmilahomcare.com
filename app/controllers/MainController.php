<?php

namespace Homecare\Controllers;

use Phalcon\Mvc\Controller;
use Homecare\utils\HttpRequest;

class MainController extends BaseController {

    public function indexAction()
    {
        if (!$this->session->has('auth-token')) {
            return $this->response->redirect('login');
        }

        $this->view->menuItems = [
            ['url' => '/details', 'text' => 'My Profile', 'icon' => 'person'],
            ['url' => '/signup', 'text' => 'Create User', 'icon' => 'people'],
            ['url' => '/logout', 'text' => 'Logout', 'icon' => 'box-arrow-right'],
            ['url' => '/test', 'text' => 'Test', 'icon' => 'settings']
        ];

        $token = $this->session->get('auth-token');
        $username = $this->getUsername($token); // object
        $userId = $this->getUserId($token); // int
        $userRole = $this->getRole($token); // int
        $isExpired = $this->isExpired($token); // bool

        $this->view->setVar("username", $username);
        $this->view->setVar("userid", $userId);
        $this->view->setVar("role", $userRole);
        $this->view->setVar("isexpired", $isExpired);
    }
} 