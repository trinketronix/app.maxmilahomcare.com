<?php

namespace Homecare\Utils;

use Exception;
use JsonException;

/**
 * HttpRequest class for handling API requests
 */
class HttpRequest {
    /**
     * Default headers for all requests
     */
    private const DEFAULT_HEADERS = [
        'Content-Type' => 'application/json',
        'Accept' => 'application/json'
    ];

    /**
     * Default cURL options
     */
    private const DEFAULT_CURL_OPTIONS = [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_SSL_VERIFYHOST => 2,
        CURLOPT_FOLLOWLOCATION => true
    ];

    /**
     * API endpoints mapping
     */
    private const ENDPOINTS = [
        'register' => '/auth/register',
        'login' => '/auth/login',
        'activateAccount' => '/auth/activate/account',
        'renewToken' => '/auth/renewtoken',
        'changeRole' => '/auth/changerole',
        'changePassword' => '/auth/changepassword',
        'updateUser' => '/user',
        'updatePhoto' => '/user/update/photo',
        'uploadPhoto' => '/user/upload/photo',
        'visit' => '/visit',
        'user' => '/user/{id}',      // Dynamic path with placeholder
        'account' => '/account/{id}',      // Dynamic path with placeholder
        'accounts' => '/accounts',
        'patient' => '/patients/{id}', // Dynamic path with placeholder
        'patients' => '/patients',
        'patient_visits' => '/patients/{patientid}/visits',
        'assign_patient' => '/user/assign/patient',
    ];

    /**
     * Log error messages with enhanced details
     *
     * @param string $message Error message
     * @return void
     */
    private static function logError(string $message): void {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $caller = isset($trace[1]) ? "{$trace[1]['class']}::{$trace[1]['function']}" : 'unknown';

        error_log("[HttpRequest Error] {$message} | Called from: {$caller}");
    }

    /**
     * Get full API endpoint URL
     *
     * @param string $name Endpoint name from ENDPOINTS array
     * @param array $params Optional parameters to replace in the URL (e.g. ['id' => 123] for '/user/{id}')
     * @return string Full endpoint URL
     */
    public static function getEndPoint(string $name, array $params = []): string {
        if (!isset(self::ENDPOINTS[$name])) {
            throw new Exception("Endpoint '{$name}' not defined");
        }

        $endpoint = self::ENDPOINTS[$name];

        // Replace placeholders with parameters if provided
        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $endpoint = str_replace("{{$key}}", $value, $endpoint);
            }
        }

        return self::getBaseUrl() . $endpoint;
    }

    /**
     * Get API base URL from environment or use default
     *
     * @return string Base URL for API
     */
    public static function getBaseUrl(): string {
        return BASE_URL;
    }

    /**
     * Make an HTTP POST request
     *
     * @param string $endpointName Endpoint name from ENDPOINTS array
     * @param string $jsonBody JSON request body
     * @param array $headers Additional headers
     * @param array $urlParams Optional URL parameters for templated URLs
     * @return array Response data as associative array
     * @throws Exception
     */
    public static function post(string $endpointName, string $jsonBody, array $headers = [], array $urlParams = []): array {
        return self::request('POST', $endpointName, $jsonBody, $headers, $urlParams);
    }

    /**
     * Make an HTTP PUT request
     *
     * @param string $endpointName Endpoint name from ENDPOINTS array
     * @param string $jsonBody JSON request body
     * @param array $headers Additional headers
     * @param array $urlParams Optional URL parameters for templated URLs
     * @return array Response data as associative array
     * @throws Exception
     */
    public static function put(string $endpointName, string $jsonBody, array $headers = [], array $urlParams = []): array {
        return self::request('PUT', $endpointName, $jsonBody, $headers, $urlParams);
    }

    /**
     * Make an HTTP DELETE request
     *
     * @param string $endpointName Endpoint name from ENDPOINTS array
     * @param string $jsonBody Optional JSON request body
     * @param array $headers Additional headers
     * @param array $urlParams Optional URL parameters for templated URLs
     * @return array Response data as associative array
     * @throws Exception
     */
    public static function delete(string $endpointName, string $jsonBody = '', array $headers = [], array $urlParams = []): array {
        return self::request('DELETE', $endpointName, $jsonBody, $headers, $urlParams);
    }

    /**
     * Make an HTTP GET request
     *
     * @param string $endpointName Endpoint name from ENDPOINTS array
     * @param array $headers Additional headers
     * @param array $urlParams Optional URL parameters for templated URLs (e.g. ['id' => 123] for '/user/{id}')
     * @param array $queryParams Optional query string parameters
     * @return array Response data as associative array
     * @throws Exception
     */
    public static function get(string $endpointName, array $headers = [], array $urlParams = [], array $queryParams = []): array {
        return self::request('GET', $endpointName, '', $headers, $urlParams, $queryParams);
    }

    /**
     * Core request method that handles all HTTP requests
     *
     * @param string $method HTTP method (GET, POST, PUT, DELETE)
     * @param string $endpointName Endpoint name from ENDPOINTS array
     * @param string $jsonBody Optional JSON request body
     * @param array $headers Additional headers
     * @param array $urlParams Optional URL parameters for templated URLs
     * @param array $queryParams Optional query string parameters
     * @return array Response data as associative array
     * @throws Exception
     */
    private static function request(
        string $method,
        string $endpointName,
        string $jsonBody = '',
        array $headers = [],
        array $urlParams = [],
        array $queryParams = []
    ): array {
        $ch = curl_init();

        // Merge default and custom headers
        $headerArray = self::prepareHeaders($headers);

        // Build the URL with parameters
        $url = self::getEndPoint($endpointName, $urlParams);

        // Add query parameters if provided
        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }

        // Set up the cURL options based on method
        $options = self::DEFAULT_CURL_OPTIONS + [
                CURLOPT_URL => $url,
                CURLOPT_HTTPHEADER => $headerArray,
                CURLOPT_CUSTOMREQUEST => $method
            ];

        // Add body data for POST, PUT, etc.
        if (!empty($jsonBody) && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            $options[CURLOPT_POSTFIELDS] = $jsonBody;
        }

        // Apply all options
        curl_setopt_array($ch, $options);

        // Execute request
        $response = curl_exec($ch);

        // Check for errors
        if (curl_errno($ch)) {
            $errorMessage = curl_error($ch);
            $errorCode = curl_errno($ch);
            curl_close($ch);
            self::logError("HTTP {$method} request error: {$errorMessage} (Code: {$errorCode})");
            throw new Exception("HTTP request failed: {$errorMessage}", $errorCode);
        }

        // Get response info
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Close the connection
        curl_close($ch);

        // Log non-2xx responses for debugging
        if ($httpCode < 200 || $httpCode >= 300) {
            self::logError("HTTP {$method} request to {$url} returned status {$httpCode} with response: " . substr($response, 0, 1000));
        }

        // Parse and return response
        return self::parseJsonResponse($response, $httpCode);
    }

    /**
     * Prepare request headers array in cURL format
     *
     * @param array $customHeaders User-provided headers
     * @return array Headers in cURL format
     */
    private static function prepareHeaders(array $customHeaders): array {
        // Merge default headers with custom headers
        $headers = array_merge(self::DEFAULT_HEADERS, $customHeaders);

        // Convert to cURL header format
        $headerArray = [];
        foreach ($headers as $key => $value) {
            $headerArray[] = "{$key}: {$value}";
        }

        return $headerArray;
    }

    /**
     * Parse JSON response and handle errors
     *
     * @param string $response API response string
     * @param int $httpCode HTTP status code
     * @return array Parsed response
     * @throws Exception
     */
    private static function parseJsonResponse(string $response, int $httpCode): array {
        if (empty($response)) {
            return ['success' => ($httpCode >= 200 && $httpCode < 300), 'message' => 'Empty response', 'data' => []];
        }

        try {
            $data = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
            return $data;
        } catch (JsonException $e) {
            self::logError("Failed to parse JSON response: {$e->getMessage()}. Response: " . substr($response, 0, 1000));
            throw new Exception("Invalid JSON response: {$e->getMessage()}", $e->getCode());
        }
    }

    /**
     * Create a direct URL for special requests (custom paths not in the endpoints list)
     *
     * @param string $path Custom API path
     * @param array $queryParams Optional query parameters
     * @return string Full URL
     */
    public static function buildCustomUrl(string $path, array $queryParams = []): string {
        $url = self::getBaseUrl() . $path;

        if (!empty($queryParams)) {
            $url .= '?' . http_build_query($queryParams);
        }

        return $url;
    }

    /**
     * Make a request to a custom URL not defined in the endpoints list
     *
     * @param string $method HTTP method
     * @param string $path Custom path
     * @param string $jsonBody Request body
     * @param array $headers Request headers
     * @param array $queryParams Query parameters
     * @return array Response data
     * @throws Exception
     */
    public static function customRequest(
        string $method,
        string $path,
        string $jsonBody = '',
        array $headers = [],
        array $queryParams = []
    ): array {
        $ch = curl_init();

        // Merge default and custom headers
        $headerArray = self::prepareHeaders($headers);

        // Build the URL with query parameters
        $url = self::buildCustomUrl($path, $queryParams);

        // Set up the cURL options based on method
        $options = self::DEFAULT_CURL_OPTIONS + [
                CURLOPT_URL => $url,
                CURLOPT_HTTPHEADER => $headerArray,
                CURLOPT_CUSTOMREQUEST => $method
            ];

        // Add body data for POST, PUT, etc.
        if (!empty($jsonBody) && in_array($method, ['POST', 'PUT', 'PATCH'])) {
            $options[CURLOPT_POSTFIELDS] = $jsonBody;
        }

        // Apply all options
        curl_setopt_array($ch, $options);

        // Execute request
        $response = curl_exec($ch);

        // Check for errors
        if (curl_errno($ch)) {
            $errorMessage = curl_error($ch);
            $errorCode = curl_errno($ch);
            curl_close($ch);
            self::logError("HTTP {$method} request error: {$errorMessage} (Code: {$errorCode})");
            throw new Exception("HTTP request failed: {$errorMessage}", $errorCode);
        }

        // Get response info
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        // Close the connection
        curl_close($ch);

        // Log non-2xx responses for debugging
        if ($httpCode < 200 || $httpCode >= 300) {
            self::logError("HTTP {$method} request to {$url} returned status {$httpCode} with response: " . substr($response, 0, 1000));
        }

        // Parse and return response
        return self::parseJsonResponse($response, $httpCode);
    }
}