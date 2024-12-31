<?php

namespace Homecare\Controllers;

use Phalcon\Mvc\Controller;
use Homecare\Models\Users;
use Homecare\utils\HttpRequest;

class ListController extends BaseController
{
    public function indexAction()
    {
        if (!$this->session->has('auth-token')) {
            return $this->response->redirect('login');
        }

        // Get cache service
        $cache = $this->di->get('viewCache');
        $cacheKey = 'users-list-page';

        // Try to get cached content
        $cachedContent = $cache->get($cacheKey);
        if ($cachedContent !== null) {
            return $cachedContent;
        }

        $users = new Users();
        $token = $this->session->get('auth-token');
        
        $usersList = $users->getAllUsers($token);
        
        if (!$usersList) {
            $this->flash->error('Unable to fetch users list');
            return $this->response->redirect('main');
        }

        $this->view->users = $usersList;

        // Cache the rendered view
        $content = $this->view->getRender('list', 'index');
        $cache->set($cacheKey, $content, 7200);

        return $content;
    }
} 