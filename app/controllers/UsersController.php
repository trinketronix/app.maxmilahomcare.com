<?php

namespace Homecare\Controllers;
use Homecare\Utils\Endpoint;
use Homecare\Utils\HttpRequest;
class UsersController extends BaseController
{
    public function indexAction()
    {
        $token = $this->session->get('auth-token');
        $headers = ["Authorization" => $token];
        //  require_once 'c:\xampp\htdocs\app.maxmila.com\app\views\users\index.php';
        if ($token != null) {
            $response = HttpRequest::get(Endpoint::ACCOUNTS, $headers,[]);
            if (!isset($response['data']['users'])) {
                // Handle the case where 'accounts' is missing or not an array
                $this->view->setVars([
                    'accounts' => [],
                    'error' => 'No se pudieron obtener las cuentas correctamente.',
                ]);
            } else {
                $accounts = $response['data']['users'];
                //    $this->view->setVar("selaccounts", $accounts);
                if (!empty($token)) {
       //             $this->session->set('user_id', $accounts['id']);
      //              $this->session->set(key: 'user_name', value: $accounts['lastname'] . ' ' . $accounts['firstname']);
                    $this->view->setVars([
     //                   'userid' => $accounts['id'],
    //                    'namename' => $accounts['lastname'] . ' ' . $accounts['firstname'],
                        'selaccounts' => $accounts,
                        'token' => $token,
                        'baseUrl' => BASE_URL,
                    ]);
                }

            }
        }
    }
}