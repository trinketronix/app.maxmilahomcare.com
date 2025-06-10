<?php

namespace App\Services;

use App\Utils\HttpRequest;
use App\Utils\Endpoint;
use Phalcon\Di\Injectable;
use Exception;

/**
 * Authentication Service
 *
 * Handles all authentication-related operations
 */
class AuthService extends Injectable
{
    /**
     * Session keys
     */
    private const SESSION_TOKEN = 'auth-token';
    private const SESSION_USER_ID = 'user_id';
    private const SESSION_USER_NAME = 'user_name';
    private const SESSION_USER_ROLE = 'user_role';
    private const SESSION_USER_FULL_NAME = 'user_full_name';

    /**
     * Attempt to authenticate a user
     *
     * @param string $username
     * @param string $password
     * @return array ['success' => bool, 'message' => string, 'data' => array]
     */
    public function login(string $username, string $password): array
    {
        try {
            // Validate input
            if (empty($username) || empty($password)) {
                return [
                    'success' => false,
                    'message' => 'Username and password are required',
                    'data' => null
                ];
            }

            // Prepare login request
            $jsonBody = json_encode([
                'username' => $username,
                'password' => $password
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
            $token = $response['data']['token'];
            $tokenData = $this->decodeToken($token);

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
            $this->createUserSession($token, $tokenData);

            return [
                'success' => true,
                'message' => 'Login successful',
                'data' => [
                    'user_id' => $tokenData['id'],
                    'username' => $tokenData['username'],
                    'role' => $tokenData['role'],
                    'redirect' => $this->getRedirectPath($tokenData['role'])
                ]
            ];

        } catch (Exception $e) {
            $this->logError('Login error: ' . $e->getMessage());

            return [
                'success' => false,
                'message' => 'An error occurred during login. Please try again.',
                'data' => null
            ];
        }
    }

    /**
     * Log out the current user
     *
     * @return void
     */
    public function logout(): void
    {
        // Clear all session data
        $this->session->remove(self::SESSION_TOKEN);
        $this->session->remove(self::SESSION_USER_ID);
        $this->session->remove(self::SESSION_USER_NAME);
        $this->session->remove(self::SESSION_USER_ROLE);
        $this->session->remove(self::SESSION_USER_FULL_NAME);

        // Destroy the session
        if ($this->session->exists()) {
            $this->session->destroy();
        }
    }

    /**
     * Check if user is authenticated
     *
     * @return bool
     */
    public function isAuthenticated(): bool
    {
        if (!$this->session->has(self::SESSION_TOKEN)) {
            return false;
        }

        $token = $this->session->get(self::SESSION_TOKEN);
        $tokenData = $this->decodeToken($token);

        if (!$tokenData || $this->isTokenExpired($tokenData)) {
            $this->logout(); // Clear invalid session
            return false;
        }

        return true;
    }

    /**
     * Get the current authenticated user
     *
     * @return array|null User data or null if not authenticated
     */
    public function getCurrentUser(): ?array
    {
        if (!$this->isAuthenticated()) {
            return null;
        }

        return [
            'id' => $this->session->get(self::SESSION_USER_ID),
            'username' => $this->session->get(self::SESSION_USER_NAME),
            'role' => $this->session->get(self::SESSION_USER_ROLE),
            'full_name' => $this->session->get(self::SESSION_USER_FULL_NAME)
        ];
    }

    /**
     * Get the current user's role
     *
     * @return int -1 if not authenticated
     */
    public function getUserRole(): int
    {
        return $this->session->get(self::SESSION_USER_ROLE, -1);
    }

    /**
     * Check if user has a specific role
     *
     * @param int $role
     * @return bool
     */
    public function hasRole(int $role): bool
    {
        return $this->getUserRole() === $role;
    }

    /**
     * Check if user has any of the specified roles
     *
     * @param array $roles
     * @return bool
     */
    public function hasAnyRole(array $roles): bool
    {
        return in_array($this->getUserRole(), $roles, true);
    }

    /**
     * Create user session
     *
     * @param string $token
     * @param array $tokenData
     * @return void
     */
    private function createUserSession(string $token, array $tokenData): void
    {
        $this->session->set(self::SESSION_TOKEN, $token);
        $this->session->set(self::SESSION_USER_ID, $tokenData['id']);
        $this->session->set(self::SESSION_USER_NAME, $tokenData['username']);
        $this->session->set(self::SESSION_USER_ROLE, $tokenData['role']);

        // Try to get user's full name (this might need an additional API call)
        $this->session->set(self::SESSION_USER_FULL_NAME, $tokenData['username']);
    }

    /**
     * Decode authentication token
     *
     * @param string $token
     * @return array|null
     */
    private function decodeToken(string $token): ?array
    {
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
    private function isTokenExpired(array $tokenData): bool
    {
        $currentTime = (int)(microtime(true) * 1000);
        return $tokenData['expiration'] < $currentTime;
    }

    /**
     * Get redirect path based on user role
     *
     * @param int $role
     * @return string
     */
    private function getRedirectPath(int $role): string
    {
        // Role 0 = Admin, Role 1 = Manager, Role 2 = Caregiver
        return $role < 2 ? 'main' : 'caregiver';
    }

    /**
     * Refresh user token
     *
     * @return bool
     */
    public function refreshToken(): bool
    {
        if (!$this->isAuthenticated()) {
            return false;
        }

        try {
            $currentToken = $this->session->get(self::SESSION_TOKEN);

            // Call API to refresh token
            $response = HttpRequest::post(Endpoint::RENEW_TOKEN, '', [
                'Authorization' => $currentToken
            ]);

            if (!empty($response['data']['token'])) {
                $newToken = $response['data']['token'];
                $tokenData = $this->decodeToken($newToken);

                if ($tokenData && !$this->isTokenExpired($tokenData)) {
                    $this->createUserSession($newToken, $tokenData);
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
    private function logError(string $message): void
    {
        error_log('[AuthService] ' . $message);
    }
}