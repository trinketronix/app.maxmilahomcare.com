<?php
namespace Homecare\Controllers;
use Exception;
use Homecare\Utils\Endpoint;
//use Phalcon\Http\Response;
use Homecare\Utils\HttpRequest;

class UserPatientController extends BaseController
{

    public function indexAction()
    {

        // Check authentication
        $token = $this->session->get('auth-token');
        if (!$token) {
            $this->flashSession->error('Please log in to view patients');
            return $this->response->redirect('login');
        }


        // Prepare headers
        $headers = ["Authorization" => $token];

        // Retrieve caregiver data from session
        $userId = $this->session->get('user_id');
        $userName = $this->session->get('user_name');

        // Set view variables
        $userId = $this->session->get('user_id');
        $userName = $this->session->get('user_name');

        echo "<h2>Welcome to the Patient Assignment Module</h2>";
// Retrieve caregiver data from session
        $userId = $this->session->get('user_id');
        $userName = $this->session->get('user_name');

        // Set view variables
        $userId = $this->session->get('user_id');
        $userName = $this->session->get('user_name');


        $assigned = $this->getAssignedPatients($userId);
        $unassigned = $this->getUserUnassignedPatients($userId);
        // Set view variables
        $this->view->setVars([
            'userId' => $userId,
            'userName' => $userName,
            'assignedPatients' => $assigned,
            'unassignedPatients' => $unassigned
        ]);

    }

    public function manageAction($userId)
    {

        // Retrieve caregiver data from session
        $userId = $this->session->get('user_id');
        $userName = $this->session->get('user_name');

        // Set view variables
        $userId = $this->session->get('user_id');
        $userName = $this->session->get('user_name');


        $assigned = $this->getAssignedPatients($userId);
        $unassigned = $this->getUnassignedPatients($userId);
        // Set view variables
        $this->view->setVars([
            'userId' => $userId,
            'userName' => $userName,
            'assignedPatients' => $assigned,
            'unassignedPatients' => $unassigned
        ]);
    }


    private function getAssignedPatients($userId)
    {
        $token = $this->session->get('auth-token');
        $url = "http://api-test.maxmilahomecare.com/assigned/patients/{$userId}";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "Authorization: $token"
        ]);
        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response, true);

        if (
            isset($data['data']['patients']) &&
            is_array($data['data']['patients'])
        ) {
            return $data['data']['patients'] ?? [];
        }

        return [];

    }


    private function getUserUnassignedPatients($userId)
    {
        {
            $token = $this->session->get('auth-token');
            $url = "http://api-test.maxmilahomecare.com/unassigned/patients/{$userId}";

            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: $token"
            ]);
            $response = curl_exec($ch);
            curl_close($ch);

            $data = json_decode($response, true);

            if (
                isset($data['data']['patients']) &&
                is_array($data['data']['patients'])
            ) {
                return $data['data']['patients'] ?? [];
            }

        }

    }
}