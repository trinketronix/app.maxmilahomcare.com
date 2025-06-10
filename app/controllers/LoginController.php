<?php

namespace App\Controllers;

use Exception;
use Phalcon\Http\Response;
use App\Utils\HttpRequest;
use App\Utils\Endpoint;

/**
 * Login Controller
 *
 * Handles user authentication and session creation
 */
class LoginController extends BaseController {
    /**
     * Default action - displays login form and processes login attempts
     *
     * @return void
     */
    public function indexAction() {
        // Check if user is already logged in
        if ($this->session->has('auth-token')) {
            // Determine where to redirect based on user role
            $token = $this->session->get('auth-token');
            $role = $this->getRole($token);
            $this->redirectBasedOnRole($role);
        }

        // Process login form submission
        if ($this->request->isPost()) {
            return $this->processLogin();
        }

        // If neither logged in nor submitting form, just display the login page
    }

    /**
     * Process the login request
     *
     * @return mixed Response or void
     */
    private function processLogin() {
        // Get credentials from form
        $username = $this->request->getPost('username', 'email');
        $password = $this->request->getPost('password', 'string');

        // Prepare request payload
        $jsonBody = $this->prepareLoginPayload($username, $password);

        try {
            // Call login API
            $loginResponse = HttpRequest::post(Endpoint::LOGIN, $jsonBody);

            // Check if we received a valid token
            if (empty($loginResponse['data']['token'])) {
                $this->flashSession->error($loginResponse['message'] ?? 'Authentication failed');
                return null;
            }

            // Extract token and establish session
            $token = $loginResponse['data']['token'];
            $this->establishUserSession($username, $token);

            // Determine proper redirect based on user role
            $role = $this->getRole($token);
            return $this->redirectBasedOnRole($role);

        } catch (Exception $e) {
            // Log the error
            error_log('Login error: ' . $e->getMessage());

            // Inform the user
            $this->flashSession->error('An exception error occurred during login: ' . $e->getMessage());

            // Stay on login page
            return $this->dispatcher->forward([
                'controller' => 'login',
                'action' => 'index'
            ]);
        }
    }

    /**
     * Establish the user session after successful login
     *
     * @param string $username The username
     * @param string $token The authentication token
     * @return void
     */
    private function establishUserSession($username, $token) {
        // Store essential data in session
        $this->session->set('auth-token', $token);
    }

    /**
     * Prepare the login request payload
     *
     * @param string $username
     * @param string $password
     * @return string JSON-encoded request body
     */
    private function prepareLoginPayload($username, $password) {
        return json_encode(
            [
                "username" => $username,
                "password" => $password
            ],
            JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES
        );
    }

    /**
     * Redirect user based on their role
     *
     * @param int $role User role value
     * @return Response
     */
    private function redirectBasedOnRole($role) {
        // If token is invalid or missing
        if ($role === -1) {
            $this->session->destroy();
            $this->flashSession->error('Your user is not activated, please check your email to activate your account.');
            return $this->response->redirect('login');
        }

        // Redirect based on role
        if ($role < 2) {
            return $this->response->redirect('main');
        } else {
            return $this->response->redirect('caregiver');
        }
    }
}