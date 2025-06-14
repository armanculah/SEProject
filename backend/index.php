<?php

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/vendor/autoload.php';
require 'rest/services/AuthService.php'; // Updated to match the new file name
require "middleware/AuthMiddleware.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Register services and middleware
Flight::map('auth_service', function() {
    return new AuthService();
});
Flight::map('auth_service_instance', function() { return Flight::get('auth_service'); });
Flight::map('auth_middleware', function() {
    return new AuthMiddleware();
});

// Global middleware for token verification
Flight::route('/*', function() {
    $url = strtolower(Flight::request()->url);
    $method = strtoupper(Flight::request()->method);
    error_log("DEBUG: Checking URL in middleware: $url, METHOD: $method");
    // Allow unauthenticated access to login/register (with or without /rest and version prefix)
    if (
        preg_match('#^(/rest)?(/v[0-9]+)?/auth/login/?$#i', $url) ||
        preg_match('#^(/rest)?(/v[0-9]+)?/auth/register/?$#i', $url)
    ) {
        error_log("DEBUG: Middleware bypassed for public auth route: $url");
        return true;
    } else {
        try {
            $token = Flight::request()->getHeader("Authorization");
            if (Flight::auth_middleware()->verifyToken($token)) {
                return true;
            }
        } catch (\Exception $e) {
            Flight::halt(401, $e->getMessage());
        }
    }
});

// Include route files
require_once __DIR__ .'/rest/routes/AuthRoutes.php';
require_once __DIR__ .'/rest/routes/routes.php';

// Start the FlightPHP framework
Flight::start();
