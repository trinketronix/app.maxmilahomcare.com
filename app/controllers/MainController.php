<?php

namespace Homecare\Controllers;

use Phalcon\Mvc\Controller;
use Homecare\utils\HttpRequest;

class MainController extends Controller
{
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
    }
} 