<?php

namespace App\Controllers;

use Phalcon\Mvc\Controller;
use App\utils\HttpRequest;

class CaregiverController extends BaseController {

    public function indexAction()
    {
        if (!$this->session->has('auth-token')) {
            return $this->response->redirect('login');
        }

        $this->view->menuItems = [
            ['url' => '/details', 'text' => 'My Profile', 'icon' => 'person'],
            ['url' => '/patients', 'text' => 'Patients', 'icon' => 'car-front-fill'],
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