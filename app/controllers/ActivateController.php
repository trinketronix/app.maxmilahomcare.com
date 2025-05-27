<?php
//use Phalcon\Http\Response;
//use Phalcon\Http\Client\Request;

namespace Homecare\Controllers;

use Exception;
use Homecare\Utils\Endpoint;
use Homecare\Utils\HttpRequest;

class AssignpatientController extends BaseController
{
    public function indexAction()
    {
        $token = $this->session->get('auth-token');
        if (!$token) {
            $this->flashSession->error('Please log in to view patients');
            return $this->response->redirect('login');
        }

        $headers = ["Authorization" => $token];
        $patients = [];

        try {
            $response = HttpRequest::get('patients', $headers);

            if (isset($response['data'])) {
                $patients = $response['data']['patients'];
            } else {
                $this->flashSession->warning('No patients found or data unavailable');
                error_log('Patient data missing in API response: ' . json_encode($response));
            }

            $this->view->setVars([
                'patients'    => $patients,
                'pageTitle'   => 'Patients to assign',
                'userId'      => $this->session->get('user_id'),
                'userName'    => $this->session->get('user_name'),
                'patientId'   => $this->session->get('patientId'),
                'patientName' => $this->session->get('patientname'),
            ]);

        } catch (Exception $e) {
            error_log("Error in AssignpatientController::indexAction: " . $e->getMessage());
            $this->flashSession->error('Unable to retrieve patients data. Please try again later.');
        }
    }

    public function setSessionAction()
    {
        $this->view->disable();

        $userId = $this->request->getPost('userId', 'int');
        $username = $this->request->getPost('username', 'string');

        if ($userId && $username) {
            $this->session->set('user_id', $userId);
            $this->session->set('user_name', $username);
            return $this->response->setJsonContent(['status' => 'ok']);
        }

        return $this->response->setStatusCode(400, 'Bad Request')
            ->setJsonContent(['status' => 'error', 'message' => 'Missing parameters']);
    }

    public function viewAction()
    {
        $id = $this->dispatcher->getParam('id');

        if (!$id) {
            $this->flashSession->error('Patient ID is required');
            return $this->response->redirect('patients');
        }

        $token = $this->session->get('auth-token');
        if (!$token) {
            $this->flashSession->error('Please log in to view patient details');
            return $this->response->redirect('login');
        }

        $headers = [
            "Authorization" => $token,
            "Content-Type"  => "application/json"
        ];

        try {
            $response = HttpRequest::get(Endpoint::PATIENT, $headers, ['id' => $id]);

            if (isset($response['data'])) {
                $this->view->patient = $response['data'];
                $this->view->pageTitle = "Patient Details";
                $this->view->userId = $this->session->get('user_id');
                $this->view->userName = $this->session->get('user_name');
            } else {
                $this->flashSession->warning('Patient details not found');
                return $this->response->redirect('patients');
            }

        } catch (Exception $e) {
            error_log("Error in AssignpatientController::viewAction: " . $e->getMessage());
            $this->flashSession->error('Unable to retrieve patient details. Please try again later.');
            return $this->response->redirect('patients');
        }
    }


    public function visitAction()
    {
        $id = $this->dispatcher->getParam('id');

        if (!$id) {
            $this->flashSession->error('Patient ID is required');
            return $this->response->redirect('patients');
        }

        $this->cookies->set('patient_id', $id, time() + 86400);
        return $this->response->redirect('visit');
    }


    public function setPatientAction()
    {
        // Only allow POST
        if (!$this->request->isPost()) {
            $this->response->setStatusCode(405, "Method Not Allowed");
            return $this->response;
        }

        // Get the JSON body
        $data = $this->request->getJsonRawBody(true);

        $patientId = $data['patientId'] ?? null;
        $patientName = $data['patientName'] ?? null;

        if (!$patientId || !$patientName) {
            return $this->response
                ->setContentType('application/json', 'utf-8')
                ->setJsonContent([
                    'status' => 'error',
                    'message' => 'Missing patient data'
                ]);
        }

        // Your logic to assign the patient here

        return $this->response
            ->setContentType('application/json', 'utf-8')
            ->setJsonContent([
                'status' => 'ok'
            ]);
    }


} "