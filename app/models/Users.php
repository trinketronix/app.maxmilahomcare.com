<?php

namespace Homecare\Models;

use Homecare\utils\HttpRequest;
use Phalcon\Di;

class Users {
    private $cache;

    public function __construct(){
        $this->cache = Di::getDefault()->get('cache');
    }

    private function makeRequest($method, $url, $headers = [], $data = null) {

        $send = new HttpRequest();
        switch ($method) {
            case 'GET': $send->get($url); break;
            case 'POST': $send->post($url, $data, $headers); break;
            case 'PUT': $send->put($url, $data, $headers); break;
            case 'DELETE': $send->delete($url, $data, $headers); break;
        }
    }

    public function getUser($token) {

    }

    public function getAllUsers($token)
    {
        $cacheKey = 'all_users';
        $usersList = $this->cache->get($cacheKey);
        
        if ($usersList !== null) {
            return $usersList;
        }

        try {
            $response = $this->makeRequest(
                'get',
                '/users',
                [
                    'Content-Type' => 'application/json',
                    'Authorization' => $token //el admin o manager
                ]
            );
            
            $usersList = json_decode($response->getBody());
            
            if ($usersList) {
                $this->cache->set($cacheKey, $usersList);
            }
            
            return $usersList;
        } catch (\Exception $e) {
            return [];
        }
    }

    public function clearCache()
    {
        $this->cache->clear();
    }
} 