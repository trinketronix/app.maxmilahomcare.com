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
            ['url' => '/signup', 'text' => 'Create Account', 'icon' => 'people'],
            ['url' => '/activate', 'text' => 'Activate Account', 'icon' => 'heart'],
            ['url' => '/user', 'text' => 'Update user', 'icon' => 'emoji-smile'],
            ['url' => '/changerole', 'text' => 'Change Role', 'icon' => 'diamond'],
            ['url' => '/test', 'text' => 'List Accounts', 'icon' => 'settings'],
            ['url' => '/users', 'text' => 'List Users', 'icon' => 'universal-access'],
            ['url' => '/logout', 'text' => 'Logout', 'icon' => 'box-arrow-right'],
            
        ];

        $token = $this->session->get('auth-token');
        $username = $this->getUsername($token); // object
        $userId = $this->getUserId($token); // int
        $userRole = $this->getRole($token); // int
        $isExpired = $this->isExpired($token); // bool
        $expiration = $isExpired ? "is expired" : "is not expired";

        $this->view->setVar("username", $username);
        $this->view->setVar("userid", $userId);
        $this->view->setVar("role", $userRole);
        $this->view->setVar("expiration", $expiration);
    }
} 