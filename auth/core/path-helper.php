<?php

/**
 * Helper functions for path and URL handling
 */

/**
 * Get the base URL of the application
 * 
 * @return string The base URL
 */
function getBaseUrl()
{
    // Determine if using HTTPS
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";

    // Get the host
    $host = $_SERVER['HTTP_HOST'];

    // Get the current directory
    $current_dir = dirname($_SERVER['PHP_SELF']);

    // Normalize the directory path
    $current_dir = str_replace('\\', '/', $current_dir);

    // Remove trailing slash if present
    if ($current_dir !== '/' && substr($current_dir, -1) === '/') {
        $current_dir = substr($current_dir, 0, -1);
    }

    // Build the base URL
    $base_url = $protocol . $host . $current_dir;

    // If we're in the root directory, just return the host
    if ($current_dir === '/' || $current_dir === '') {
        $base_url = $protocol . $host;
    }

    return $base_url;
}

/**
 * Create a full URL for a given path
 * 
 * @param string $path The path to append to the base URL
 * @return string The full URL
 */
function url($path)
{
    // Remove leading slash if present
    if (substr($path, 0, 1) === '/') {
        $path = substr($path, 1);
    }

    return getBaseUrl() . '/' . $path;
}

/**
 * Get the server path for a file
 * 
 * @param string $file The file name
 * @return string The full server path
 */
function serverPath($file)
{
    return __DIR__ . '/' . $file;
}
