<?php

namespace Homecare\Controllers;

use Phalcon\Mvc\Controller;
use Homecare\Models\Users;

class DetailsController extends Controller
{
    public function indexAction()
    {
        if (!$this->session->has('auth-token')) {
            return $this->response->redirect('signup');
        }

        $username = $this->session->get('username');
        
        // Get cache service
        $cache = $this->di->get('viewCache');
        $cacheKey = 'user-details-' . $username;

        // Try to get cached content
        $cachedContent = $cache->get($cacheKey);
        if ($cachedContent !== null) {
            return $cachedContent;
        }

        $users = new Users();
        $token = $this->session->get('auth-token');
        
        $userDetails = $users->getUser($username, $token);
        
        if (!$userDetails) {
            //$this->flash->error('Unable to fetch user details');
            return $this->response->redirect('main');
        }

        $this->view->user = $userDetails;

        // Cache the rendered view
        $content = $this->view->getRender('details', 'index');
        $cache->set($cacheKey, $content, 7200);

        return $content;
    }
} 