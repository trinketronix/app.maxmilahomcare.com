<?php

namespace App\Controllers;
use App\Utils\Endpoint;
use App\Utils\HttpRequest;

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
            // Make API request using the updated HttpRequest class with URL parameters
            // Ensure 'account' endpoint is defined in HttpRequest::ENDPOINTS
            $response = HttpRequest::get(Endpoint::ACCOUNT, $headers, ['id' => $id]);
            $account = $response['data'];
            // Check if data exists and has the right structure
            if (!isset($account)) {
                // No data in response
                error_log("No data in API response for user ID: " . $id);
                $this->flashSession->error('Could not load user data. Please try again later.');
            }

            // Determine the return page based on role
            $role = $this->getRole($token);
            $liga = ($role < 2) ? 'main' : 'caregiver';

            $this->view->setVars([
                'id' => $id,
                'account' => $account,
                'liga' => $liga,
                'role' => $role,
                ]);

        } catch (\Exception $e) {
            // Log and display error
            error_log("Error in DetailsController: " . $e->getMessage());
            $this->flashSession->error('An error occurred while loading profile data: ' . $e->getMessage());
        }
    }
}