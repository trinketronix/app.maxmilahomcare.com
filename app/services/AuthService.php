<?php

namespace App\Services;

use App\Constants\Auth;
use App\Constants\Session;
use App\Constants\User;
use App\Utils\HttpRequest;
use App\Utils\Endpoint;
use Phalcon\Di\Injectable;
use Exception;

/**
 * Authentication Service
 *
 * Handles all authentication-related operations
 */
class AuthService extends Injectable {

    /**
     * Attempt to authenticate a user
     *
     * @param string $username
     * @param string $password
     * @return array ['success' => bool, 'message' => string, 'data' => array]
     */
    public function signin(string $username, string $password): array {
        try {
            // Validate input
            if (empty($username) || empty($password)) {
                return [
                    'success' => false,
                    'message' => 'Username and password are required',
                    'data' => null
                ];
            }

            // Prepare signin request
            $jsonBody = json_encode([
                Auth::USERNAME => $username,
                Auth::PASSWORD => $password
            ], JSON_THROW_ON_ERROR | JSON_UNESCAPED_SLASHES);

            // Make API request
            $response = HttpRequest::post(Endpoint::LOGIN, $jsonBody);

            // Check response
            if (empty($response['data']['token'])) {
                return [
                    'success' => false,
                    'message' => $response['message'] ?? 'Invalid credentials',
                    'data' => null
                ];
            }

            // Extract token and decode it
            $token = $response['data'][Auth::TOKEN];
            $tokenData = $this->decodeToken($token);

            //Extract user fullname and photo
            $userData = $response['data']['user'];

            if (!$tokenData) {
                return [
                    'success' => false,
                    'message' => 'Invalid authentication token received',
                    'data' => null
                ];
            }

            // Check if token is already expired
            if ($this->isTokenExpired($tokenData)) {
                return [
                    'success' => false,
                    'message' => 'Authentication token expired',
                    'data' => null
                ];
            }

            // Establish session
            $this->createUserSession($token, $tokenData, $userData);

            return [
                'success' => true,
                'message' => 'Signin successful',
                'data' => [
                    'user_id' => $tokenData[Auth::ID],
                    'username' => $tokenData[Auth::USERNAME],
                    'role' => $tokenData[Auth::ROLE],
                    'fullname' => $userData[User::FULLNAME],
                    'photo' => $userData[User::PHOTO],
                    'redirect' => $this->getRedirectPath($tokenData[Auth::ROLE])
                ]
            ];

        } catch (Exception $e) {
            $this->logError('Signin error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'An error occurred during signin. Please try again.',
                'data' => null
            ];
        }
    }

    /**
     * Log out the current user
     *
     * @return void
     */
    public function signout(): void {
        error_log("=== SIGNOUT DEBUG ===");
        error_log("Before signout - Session exists: " . ($this->session->exists() ? 'YES' : 'NO'));

        // Only attempt to destroy if a session actually exists
        if ($this->session->exists()) {

            // This is the correct and only method needed to destroy the session.
            // It clears all associated data.
            $this->session->destroy();

            // Regenerating the ID after destroying is good practice to prevent session fixation
            // This will start a new, empty session.
            if (session_status() === PHP_SESSION_ACTIVE) {
                session_regenerate_id(true);
            }
        }

        error_log("After signout - Session exists: " . ($this->session->exists() ? 'YES' : 'NO'));
        // Note: After destroy(), exists() might still be true if session_regenerate_id starts a new one,
        // but it will be an empty session, which is correct.
        error_log("===================");
    }

    /**
     * Check if user is authenticated
     *
     * @return bool
     */
    public function isAuthenticated(): bool {

        // First check if session exists
        if (!$this->session->exists()) {
            error_log("isAuthenticated: No session exists");
            return false;
        }

        // Check if we have the auth token
        if (!$this->session->has(Session::AUTH_TOKEN)) {
            error_log("isAuthenticated: No auth token in session");
            return false;
        }

        $token = $this->session->get(Session::AUTH_TOKEN);

        // Check if token is empty or null
        if (empty($token)) {
            error_log("isAuthenticated: Token is empty");
            $this->signout(); // Clear invalid session
            return false;
        }

        $tokenData = $this->decodeToken($token);

        if (!$tokenData || $this->isTokenExpired($tokenData)) {
            error_log("isAuthenticated: Token is invalid or expired");
            $this->signout(); // Clear invalid session
            return false;
        }

        return true;
    }

    /**
     * Get the current authenticated user
     *
     * @return array|null User data or null if not authenticated
     */
    public function getCurrentUser(): ?array {

        if (!$this->isAuthenticated()) return null;

        return [
            'id' => $this->session->get(Session::USER_ID),
            'username' => $this->session->get(Session::USERNAME),
            'fullname' => $this->session->get(Session::USER_FULLNAME),
            'photo' => $this->session->get(Session::USER_PHOTO),
            'role' => $this->session->get(Session::USER_ROLE)
        ];
    }

    /**
     * Get the current user's id
     *
     * @return int 0 if not authenticated
     */
    public function getUserId(): int {
        return (int) $this->session->get(Session::USER_ID, 0);
    }

    /**
     * Get the current user's username
     *
     * @return string 'unknown' if not authenticated
     */
    public function getUsername(): string {
        return $this->session->get(Session::USERNAME, 'unknown');
    }

    /**
     * Get the current user's fullname
     *
     * @return string 'unknown' if not authenticated
     */
    public function getUserFullname(): string {
        return $this->session->get(Session::USER_FULLNAME, 'unknown');
    }

    /**
     * Get the current user's photo url
     *
     * @return string 'unknown' if not authenticated
     */
    public function getUserPhoto(): string {
        return $this->session->get(Session::USER_PHOTO, 'unknown');
    }

    /**
     * Get the current user's role
     *
     * @return int -1 if not authenticated
     */
    public function getUserRole(): int {
        return (int) $this->session->get(Session::USER_ROLE, -1);
    }

    /**
     * Check if user has a specific role
     *
     * @param int $role
     * @return bool
     */
    public function hasRole(int $role): bool {
        return $this->getUserRole() === $role;
    }

    /**
     * Check if user has any of the specified roles
     *
     * @param array $roles
     * @return bool
     */
    public function hasAnyRole(array $roles): bool {
        return in_array($this->getUserRole(), $roles, true);
    }

    /**
     * Create user session
     *
     * @param string $token
     * @param array $tokenData
     * @param array $userData
     * @return void
     */
    private function createUserSession(string $token, array $tokenData, array $userData): void {
        // Ensure we start with a clean session
        if ($this->session->exists()) {
            $this->session->regenerateId();
        } else {
            $this->session->start();
        }

        $this->session->set(Session::AUTH_TOKEN, $token);
        $this->session->set(Session::USER_ID, $tokenData[Auth::ID]);
        $this->session->set(Session::USERNAME, $tokenData[Auth::USERNAME]);
        $this->session->set(Session::USER_ROLE, $tokenData[Auth::ROLE]);
        $this->session->set(Session::USER_FULLNAME, $userData[User::FULLNAME]);
        $this->session->set(Session::USER_PHOTO, $userData[User::PHOTO]);
    }

    /**
     * Decode authentication token
     *
     * @param string $token
     * @return array|null
     */
    private function decodeToken(string $token): ?array {
        try {
            $decoded = base64_decode($token, true);
            if ($decoded === false) {
                return null;
            }

            $data = json_decode($decoded, true, 512, JSON_THROW_ON_ERROR);

            // Validate required fields
            if (!isset($data['id'], $data['username'], $data['role'], $data['expiration'])) {
                return null;
            }

            return $data;
        } catch (\JsonException $e) {
            $this->logError('Token decode error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Check if token is expired
     *
     * @param array $tokenData
     * @return bool
     */
    private function isTokenExpired(array $tokenData): bool {
        $currentTime = (int)(microtime(true) * 1000);
        return $tokenData['expiration'] < $currentTime;
    }

    /**
     * Get redirect path based on user role
     *
     * @param int $role
     * @return string
     */
    private function getRedirectPath(int $role): string {
        // Role 0 = Admin, Role 1 = Manager, Role 2 = Caregiver
        return $this->roleRedirectService->getRedirectPathByRole($role);
    }

    /**
     * Refresh user token
     *
     * @return bool
     */
    public function refreshToken(): bool {
        if (!$this->isAuthenticated()) {
            return false;
        }

        try {
            $currentToken = $this->session->get(Session::AUTH_TOKEN);

            // Call API to refresh token
            $response = HttpRequest::post(Endpoint::RENEW_TOKEN, '', [
                'Authorization' => $currentToken
            ]);

            if (!empty($response['data']['token'])) {
                $newToken = $response['data']['token'];
                $tokenData = $this->decodeToken($newToken);
                $userData = $response['data']['user'];

                if ($tokenData && !$this->isTokenExpired($tokenData)) {
                    $this->createUserSession($newToken, $tokenData, $userData);
                    return true;
                }
            }
        } catch (Exception $e) {
            $this->logError('Token refresh error: ' . $e->getMessage());
        }

        return false;
    }

    /**
     * Log error messages
     *
     * @param string $message
     * @return void
     */
    private function logError(string $message): void {
        error_log('[AuthService] ' . $message);
    }
}