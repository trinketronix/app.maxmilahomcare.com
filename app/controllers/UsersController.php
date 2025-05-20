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
                $this->view->setVars([
                    'id' => $accounts['id'],
                    'selaccounts' => $accounts,

                ]);

            }
        }
    }
}