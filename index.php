<?php

/**
 * Redirect to the public directory
 * 
 * This file redirects all requests to the public directory where the actual
 * Laravel application is served from.
 */

// Get the current URL
$uri = urldecode(
    parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)
);

// Remove the project name from the URI if it exists
$uri = preg_replace('/^\/smart_school_new/', '', $uri);

// Redirect to the public directory
header('Location: public' . $uri);
exit; 