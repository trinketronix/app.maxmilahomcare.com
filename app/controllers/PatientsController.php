<?php

namespace Homecare\Controllers;

use Exception;
use Homecare\Utils\Endpoint;
use Phalcon\Http\Response;
use Homecare\Utils\HttpRequest;

/**
 * Patients Controller
 *
 * Manages the listing and interactions with patient data
 */
class PatientsController extends BaseController
{
    /**
     * Display the list of patients
     *
     * @return void
     */
    public function indexAction() {
        // Check authentication
        $token = $this->session->get('auth-token');
        if (!$token) {
            $this->flashSession->error('Please log in to view patients');
            return $this->response->redirect('login');
        }

        // Prepare headers
        $headers = ["Authorization" => $token];

        try {
            // Initialize default values
            $patients = [];

            // Fetch patients data
            $response = HttpRequest::get(Endpoint::PATIENTS, $headers);

            // Process response
            if (isset($response['data'])) {
                $patients = $response['data']['patients'];
            } else {
                $this->flashSession->warning('No patients found or data unavailable');
                error_log('Patient data missing in API response: ' . json_encode($response));
            }

            // Set view variables
            $this->view->setVar("patients", $patients);
            $this->view->setVar("pageTitle", "Patients List");

        } catch (Exception $e) {
            // Log error and display user-friendly message
            error_log("Error in PatientsController: " . $e->getMessage());
            $this->flashSession->error('Unable to retrieve patients data. Please try again later.');
        }
    }

    /**
     * View a single patient's details
     *
     * @param int $id Patient ID
     * @return void
     */
    public function viewAction($id = null) {
        // Validate ID
        if (!$id) {
            $this->flashSession->error('Patient ID is required');
            return $this->response->redirect('patients');
        }

        // Check authentication
        $token = $this->session->get('auth-token');
        if (!$token) {
            $this->flashSession->error('Please log in to view patient details');
            return $this->response->redirect('login');
        }

        $headers = ["Authorization" => $token];

        try {
            // Fetch patient details
            $response = HttpRequest::get('patient', $headers, ['id' => $id]);

            if (isset($response['data'])) {
                $this->view->setVar("patient", $response['data']);
                $this->view->setVar("pageTitle", "Patient Details");
            } else {
                $this->flashSession->warning('Patient details not found');
                return $this->response->redirect('patients');
            }

        } catch (Exception $e) {
            error_log("Error in PatientsController::viewAction: " . $e->getMessage());
            $this->flashSession->error('Unable to retrieve patient details. Please try again later.');
            return $this->response->redirect('patients');
        }
    }

    /**
     * Register a visit for a patient
     *
     * @param int $id Patient ID
     * @return void
     */
    public function visitAction($id = null) {
        if (!$id) {
            $this->flashSession->error('Patient ID is required');
            return $this->response->redirect('patients');
        }

        // Store patient ID in cookie or session for the visit form
        $this->cookies->set('patient_id', $id, time() + 86400);

        // Redirect to visit registration page
        return $this->response->redirect('visit');
    }
}