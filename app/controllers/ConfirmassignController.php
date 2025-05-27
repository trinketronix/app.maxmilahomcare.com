<?php

namespace Homecare\Controllers;

use Homecare\Utils\Endpoint;
use Homecare\Utils\HttpRequest;
use Exception;

class ConfirmassignController extends BaseController
{
    public function indexAction()
    {
        $liga = 'main';
        $token = $this->session->get('auth-token');

        if (!$token) {
            $this->flashSession->error('Please log in to view your profile');
            return $this->response->redirect('login');
        }

        $userId = $this->getUserId($token);
        $username = $this->session->get('username'); //
        $patientId = $this->session->get('patientId');
        $patientname = $this->session->get('patientname'); //

        if (!$patientId || !$patientname) {
            $this->flashSession->error('No patient selected.');
            if (!empty($this->response->error)) {
                return $this->response->error;
            } // redirect or show a fallback view
        }

        $jsonPayload = json_encode([
            'user_id' => $userId,
            'patient_id' => $patientId,
            'notes' => 'Asignación correctamente efectuada'
        ]);

        $headers = [
            "Authorization" => $token,
            "Content-Type" => "application/json"
        ];

        try {
            $response = HttpRequest::post(Endpoint::ASSIGN_PATIENT, $jsonPayload, $headers);
            $account = $response['data'] ?? null;
        } catch (Exception $e) {
            error_log('Error assigning patient: ' . $e->getMessage());
            $this->flashSession->error('An error occurred: ' . $e->getMessage());
            $account = null;
        }

        $role = $this->getRole($token);
        $liga = ($role < 2) ? 'main' : 'caregiver';

        // ✅ Set all variables needed in the view
        $this->view->setVars([
            'userId'     => $userId,
            'username'   => $username,
            'patientId'  => $patientId,
            'patientname'=> $patientname,
            'account'    => $account,
            'liga'       => $account,
            'role'       => $role,
        ]);
    }

    public function setPatientAction()
    {
        $this->view->disable();

        // Optional: you may use this method if you POST patient data to set session values
        $patientId = $this->request->getPost('patientId', 'int');
        $patientname = $this->request->getPost('patientname', 'string');

        if ($patientId && $patientname) {
            $this->session->set('patientId', $patientId);
            $this->session->set('patientName', $patientname);

            return $this->response->setJsonContent(['status' => 'ok']);
        }

        return $this->response->setStatusCode(400, 'Bad Request')
            ->setJsonContent(['status' => 'error', 'message' => 'Missing parameters']);
    }
}


