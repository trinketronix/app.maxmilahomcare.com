<?php
use Phalcon\Config\Config;

return new Config([
    'application' => [
        'appDir'         => APP_PATH . '/',
        'controllersDir' => APP_PATH . '/controllers/',
        'modelsDir'      => APP_PATH . '/models/',
        'viewsDir'       => APP_PATH . '/views/',
        'cacheDir'       => BASE_PATH . '/cache/',
        'baseUri'        => '/',
    ],
    'api' => [
        'baseUrl' => getenv('BASE_URL_API') ?: 'https://api-test.maxmilahomecare.com',
        'timeout' => 30,
        'retries' => 3,
        'headers' => [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
            'User-Agent' => 'Homecare-App/1.0'
        ]
    ],
    'cache' => [
        'lifetime'    => 7200,
        'storageDir'  => BASE_PATH . '/cache/data/',
        'viewDir'     => BASE_PATH . '/cache/view/',
    ],
    'session' => [
        'savePath' => '/tmp',
    ]
]);