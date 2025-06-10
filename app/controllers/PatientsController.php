<?php

namespace App\Controllers;

use Exception;
use App\Utils\Endpoint;
use Phalcon\Http\Response;
use App\Utils\HttpRequest;

/**
 * Patients Controller
 *
 * Manages the listing and interactions with patient data
 */
class PatientsController extends BaseController {
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
            $response = HttpRequest::get('patients', $headers);

            // Process response
            if (isset($response['data'])) {
                $patients = $response['data']['patients'];
            } else {
                $this->flashSession->warning('No patients found or data unavailable');
                error_log('Patient data missing in API response: ' . json_encode($response));
            }

            // Set view variables
            $this->view->patients = $patients;
            $this->view->pageTitle = "Patients List";

        } catch (Exception $e) {
            // Log error and display user-friendly message
            error_log("Error in PatientsController: " . $e->getMessage());
            $this->flashSession->error('Unable to retrieve patients data. Please try again later.');
        }
    }

    /**
     * View a single patient's details
     *
     * @return void
     */
    public function viewAction() {
        // Get patient ID from route parameter
        $id = $this->dispatcher->getParam('id');

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
            // Fetch patient details - make sure the 'patient' endpoint is properly defined
            $response = HttpRequest::get(Endpoint::PATIENT, $headers, ['id' => $id]);

            if (isset($response['data'])) {
                $this->view->patient = $response['data'];
                $this->view->pageTitle = "Patient Details";

                // Try to fetch patient visits if available
//                try {
//                    $visitsResponse = HttpRequest::get('patient_visits', $headers, ['patient_id' => $id]);
//                    if (isset($visitsResponse['data'])) {
//                        $this->view->patientVisits = $visitsResponse['data'];
//                    }
//                } catch (Exception $e) {
//                    // Just log this error but continue showing patient details
//                    error_log("Error fetching patient visits: " . $e->getMessage());
//                }
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
     * @return void
     */
    public function visitAction() {
        // Get patient ID from route parameter
        $id = $this->dispatcher->getParam('id');

        if (!$id) {
            $this->flashSession->error('Patient ID is required');
            return $this->response->redirect('patients');
        }

        // Store patient ID in cookie for the visit form
        $this->cookies->set('patient_id', $id, time() + 86400);

        // Redirect to visit registration page
        return $this->response->redirect('visit');
    }
}