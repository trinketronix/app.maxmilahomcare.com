<?php

namespace Homecare\Controllers;
use Homecare\Utils\HttpRequest;

/**
 * Details Controller
 *
 * Handles user profile details display
 */
class DetailsController extends BaseController {
    /**
     * Main action to display user details
     */
    public function indexAction() {
        // Initialize variables
        $accounts = [];
        $liga = 'main'; // Default value to avoid undefined variable

        // Get token and user ID
        $token = $this->session->get('auth-token');

        // Redirect if not authenticated
        if (!$token) {
            $this->flashSession->error('Please log in to view your profile');
            return $this->response->redirect('login');
        }

        $id = $this->getUserId($token);
        $headers = ["Authorization" => $token];

        try {
            // Make API request
            $getManaResponse = HttpRequest::get('/accounts', $headers);

            // Debug output to check response structure
            error_log("API Response: " . print_r($getManaResponse, true));

            // Check if data exists and has the right structure
            if (isset($getManaResponse['data'])) {
                // Check which key exists in the data
                if (isset($getManaResponse['data']['accounts'])) {
                    $accounts = $getManaResponse['data']['accounts'];
                } elseif (isset($getManaResponse['data']['users'])) {
                    $accounts = $getManaResponse['data']['users'];
                } else {
                    // If neither key exists, log the error
                    error_log("Unexpected data structure: " . print_r($getManaResponse['data'], true));
                    $this->flashSession->error('Could not load user data. Please try again later.');
                }
            } else {
                // No data in response
                error_log("No data in API response");
                $this->flashSession->error('Could not load user data. Please try again later.');
            }

            // Determine the return page based on role
            $role = $this->getRole($token);
            $liga = ($role < 2) ? 'main' : 'caregiver';

        } catch (\Exception $e) {
            // Log and display error
            error_log("Error in DetailsController: " . $e->getMessage());
            $this->flashSession->error('An error occurred while loading profile data: ' . $e->getMessage());
        }

        // Set view variables
        $this->view->setVar("managers", $accounts);
        $this->view->setVar("myid", $id);
        $this->view->setVar("liga", $liga);

        // Also set as direct properties for compatibility
        $this->view->managers = $accounts;
        $this->view->myid = $id;
        $this->view->liga = $liga;
    }
}