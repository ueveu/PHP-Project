<?php
/**
 * Configuration file for the blog application
 * Contains important constants and settings
 */

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Session configuration
session_start();

// Constants for file paths
define('ROOT_PATH', dirname(__DIR__) . DIRECTORY_SEPARATOR);
define('DATA_PATH', ROOT_PATH . 'data' . DIRECTORY_SEPARATOR);
define('TEMPLATES_PATH', ROOT_PATH . 'templates' . DIRECTORY_SEPARATOR);

// File paths for data storage
define('USERS_FILE', DATA_PATH . 'users.txt');
define('POSTS_FILE', DATA_PATH . 'posts.txt');

// Application settings
define('SITE_NAME', 'My Weblog');
define('ITEMS_PER_PAGE', 5);

// Ensure data directory exists and is writable
if (!file_exists(DATA_PATH)) {
    mkdir(DATA_PATH, 0777, true);
}

// Initialize data files if they don't exist
if (!file_exists(USERS_FILE)) {
    file_put_contents(USERS_FILE, '');
}
if (!file_exists(POSTS_FILE)) {
    file_put_contents(POSTS_FILE, '');
}

/**
 * Function to display error messages
 * @param string $message The error message to display
 */
function showError($message) {
    $_SESSION['error'] = $message;
}

/**
 * Function to display success messages
 * @param string $message The success message to display
 */
function showSuccess($message) {
    $_SESSION['success'] = $message;
}

/**
 * Function to check if user is logged in
 * @return boolean
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
} 