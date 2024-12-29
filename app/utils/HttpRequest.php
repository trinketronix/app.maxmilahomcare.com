<?php

namespace Homecare\utils;


use Exception;

class HttpRequest{

    private const string BASE_URL = "https://api.maxmilahomecare.com";
    private const string CONTENT_TYPE = 'Content-Type: application/json';

    /**
     * @param string $endpoint as `/users`, `/login`, `caregivers`
     * @param string $jsonBody
     * @param array $headers
     * @return string
     * @throws Exception
     * POST Request function
     */
    public static function post(string $endpoint, string $jsonBody, array $headers = []): string {
        $endpoint = self::BASE_URL . $endpoint;
        // Initialize cURL
        $ch = curl_init();
        // Set options for the cURL transfer
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        // Set the POST data
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonBody);
        // Set custom headers
        $headerArray = [];
        foreach ($headers as $key => $value) { $headerArray[] = "$key: $value"; }
        $headerArray[] = self::CONTENT_TYPE; // Add this if not already specified
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);
        // Execute POST request
        $response = curl_exec($ch);
        // Check if any error occurred
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            throw new Exception("Post request error: " . $error_msg);
        }
        // Close cURL handle
        curl_close($ch);
        return $response;
    }

    /**
     * @param string $endpoint as `/users`, `/login`, `caregivers`
     * @param string $jsonBody
     * @param array $headers
     * @return string
     * @throws Exception
     * PUT Request
     */
    public static function put(string $endpoint, string $jsonBody, array $headers = []): string {
        $endpoint = self::BASE_URL . $endpoint;
        // Initialize cURL
        $ch = curl_init();
        // Set options for the cURL transfer
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT"); // Specify PUT method
        // Set the POST data (even though it's a PUT request, we use POSTFIELDS for the body)
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonBody);
        // Set custom headers
        $headerArray = [];
        foreach ($headers as $key => $value) { $headerArray[] = "$key: $value"; }
        $headerArray[] = self::CONTENT_TYPE; // Add this if not already specified
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);
        // Execute PUT request
        $response = curl_exec($ch);
        // Check if any error occurred
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            throw new Exception("Put request error: " . $error_msg);
        }
        // Close cURL handle
        curl_close($ch);
        return $response;
    }

    /**
     * @param string $endpoint as `/users`, `/login`, `caregivers`
     * @param string $jsonBody
     * @param array $headers
     * @return string
     * @throws Exception
     * DELETE Request
     */
    public static function delete(string $endpoint, string $jsonBody = '', array $headers = []): string {
        $endpoint = self::BASE_URL . $endpoint;
        // Initialize cURL
        $ch = curl_init();
        // Set options for the cURL transfer
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE"); // Specify DELETE method
        // Set the body if provided (though DELETE requests typically don't send a body)
        if (!empty($jsonBody)) { curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonBody); }
        // Set custom headers
        $headerArray = [];
        foreach ($headers as $key => $value) { $headerArray[] = "$key: $value"; }
        // Ensure Content-Type is set if a body is provided
        if (!empty($jsonBody)) { $headerArray[] = self::CONTENT_TYPE; }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);
        // Execute DELETE request
        $response = curl_exec($ch);
        // Check if any error occurred
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            throw new Exception("Delete request error: " . $error_msg);
        }
        // Close cURL handle
        curl_close($ch);
        return $response;
    }

    /**
     * @param string $endpoint as `/users`, `/login`, `caregivers`
     * @param array $headers
     * @return string
     * @throws Exception
     * GET Request
     */
    public static function get(string $endpoint, array $headers = []): array {
        $endpoint = self::BASE_URL . $endpoint;
        // Initialize cURL
        $ch = curl_init();
        // Set options for the cURL transfer
        curl_setopt($ch, CURLOPT_URL, $endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        // Set custom headers
        $headerArray = [];
        foreach ($headers as $key => $value) { $headerArray[] = "$key: $value"; }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headerArray);
        // Execute GET request
        $json = curl_exec($ch);
        // Check if any error occurred
        if (curl_errno($ch)) {
            $error_msg = curl_error($ch);
            curl_close($ch);
            throw new Exception("Get request error: " . $error_msg);
        }
        // Close cURL handle
        curl_close($ch);
        return json_decode($json, true, 512, JSON_THROW_ON_ERROR);
    }
}