<?php
// Router for PHP built-in server
// This file handles URL rewriting when using php -S

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// If the file exists, serve it directly
if ($uri !== '/' && file_exists(__DIR__ . $uri)) {
    return false;
}

// Extract route from URI
$route = trim($uri, '/');

// If empty, use 'home' as default
if (empty($route)) {
    $route = 'home';
}

// Set the route in $_GET for index.php to use
$_GET['route'] = $route;

// Include index.php
require __DIR__ . '/index.php';
