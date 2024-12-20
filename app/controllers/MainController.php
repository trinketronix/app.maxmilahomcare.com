<?php

namespace Homecare\Controllers;

use Phalcon\Mvc\Controller;

class MainController extends Controller
{
    public function indexAction()
    {
        if (!$this->session->has('auth-token')) {
            return $this->response->redirect('login');
        }

        $this->view->menuItems = [
            ['url' => '/details', 'text' => 'My Profile', 'icon' => 'person'],
            ['url' => '/list', 'text' => 'User List', 'icon' => 'people'],
            ['url' => '/logout', 'text' => 'Logout', 'icon' => 'box-arrow-right']
        ];
    }
} 