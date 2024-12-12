<?php
/**
 * User management functions
 * Handles user registration, login, and authentication
 */

require_once 'config.php';

/**
 * Read users from file with error handling
 * @return array Array of valid user records
 */
function readUsers() {
    if (!file_exists(USERS_FILE)) {
        return [];
    }
    
    $users = [];
    $lines = file(USERS_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];
    
    foreach ($lines as $line) {
        $userData = json_decode($line, true);
        if ($userData && isset($userData['username']) && isset($userData['id'])) {
            $users[] = $userData;
        }
    }
    
    return $users;
}

/**
 * Check if a username already exists
 * @param string $username Username to check
 * @return bool True if username exists, false otherwise
 */
function userExists($username) {
    if (empty($username)) {
        return false;
    }
    
    $users = readUsers();
    foreach ($users as $user) {
        if (isset($user['username']) && strtolower($user['username']) === strtolower($username)) {
            return true;
        }
    }
    return false;
}

/**
 * Check if an email already exists
 * @param string $email Email to check
 * @return bool True if email exists, false otherwise
 */
function emailExists($email) {
    if (empty($email)) {
        return false;
    }
    
    $users = readUsers();
    foreach ($users as $user) {
        if (isset($user['email']) && strtolower($user['email']) === strtolower($email)) {
            return true;
        }
    }
    return false;
}

/**
 * Check if an alias already exists
 * @param string $alias Alias to check
 * @return bool True if alias exists, false otherwise
 */
function aliasExists($alias) {
    if (empty($alias)) {
        return false;
    }
    
    $users = readUsers();
    foreach ($users as $user) {
        if (isset($user['alias']) && strtolower($user['alias']) === strtolower($alias)) {
            return true;
        }
    }
    return false;
}

/**
 * Register a new user
 * @param string $firstname First name
 * @param string $lastname Last name
 * @param string $alias Alias
 * @param string $password Password
 * @param string $email Email address
 * @return array Result with success status and message
 */
function registerUser($firstname, $lastname, $alias, $password, $email) {
    // Additional validation
    if (aliasExists($alias)) {
        return ['success' => false, 'message' => 'Dieser Alias ist bereits vergeben.'];
    }
    
    if (emailExists($email)) {
        return ['success' => false, 'message' => 'Diese E-Mail-Adresse wird bereits verwendet.'];
    }
    
    // Create user data
    $user = [
        'id' => uniqid(),
        'firstname' => $firstname,
        'lastname' => $lastname,
        'alias' => $alias,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'email' => $email,
        'created_at' => date('Y-m-d H:i:s'),
        'is_admin' => false // Default to non-admin
    ];
    
    // Make first user an admin
    $users = readUsers();
    if (empty($users)) {
        $user['is_admin'] = true;
    }
    
    // Save user to file
    if (file_put_contents(USERS_FILE, json_encode($user) . PHP_EOL, FILE_APPEND)) {
        return ['success' => true, 'message' => 'Registrierung erfolgreich! Sie können sich jetzt einloggen.'];
    } else {
        return ['success' => false, 'message' => 'Fehler bei der Registrierung. Bitte versuchen Sie es später erneut.'];
    }
}

/**
 * Get user by ID
 * @param string $userId User ID to find
 * @return array|null User data or null if not found
 */
function getUserById($userId) {
    if (empty($userId)) {
        return null;
    }
    
    $users = readUsers();
    foreach ($users as $user) {
        if (isset($user['id']) && $user['id'] === $userId) {
            return $user;
        }
    }
    return null;
}

/**
 * Get user by username
 * @param string $username Username to find
 * @return array|null User data or null if not found
 */
function getUserByUsername($username) {
    if (empty($username)) {
        return null;
    }
    
    $users = readUsers();
    foreach ($users as $user) {
        if (isset($user['username']) && strtolower($user['username']) === strtolower($username)) {
            return $user;
        }
    }
    return null;
}

/**
 * Get user by alias
 * @param string $alias Alias to find
 * @return array|null User data or null if not found
 */
function getUserByAlias($alias) {
    if (empty($alias)) {
        return null;
    }
    
    $users = readUsers();
    foreach ($users as $user) {
        if (isset($user['alias']) && strtolower($user['alias']) === strtolower($alias)) {
            return $user;
        }
    }
    return null;
}

/**
 * Login user
 * @param string $alias Alias
 * @param string $password Password
 * @param bool $remember Whether to set remember-me cookie
 * @return array Result with success status and message
 */
function loginUser($alias, $password, $remember = false) {
    $user = getUserByAlias($alias);
    
    if (!$user || !isset($user['password'])) {
        return ['success' => false, 'message' => 'Alias oder Passwort ist falsch.'];
    }
    
    if (!password_verify($password, $user['password'])) {
        return ['success' => false, 'message' => 'Alias oder Passwort ist falsch.'];
    }
    
    // Set session variables
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['alias'] = $user['alias'];
    $_SESSION['firstname'] = $user['firstname'];
    $_SESSION['lastname'] = $user['lastname'];
    $_SESSION['is_admin'] = $user['is_admin'] ?? false;
    
    // Handle remember-me functionality
    if ($remember) {
        $token = bin2hex(random_bytes(32));
        $expires = time() + (30 * 24 * 60 * 60); // 30 days
        
        // Save token in user's data
        $user['remember_token'] = [
            'token' => $token,
            'expires' => $expires
        ];
        updateUser($user);
        
        // Set cookie
        setcookie('remember_token', $token, $expires, '/', '', true, true);
    }
    
    return ['success' => true, 'message' => 'Login erfolgreich!'];
}

/**
 * Update user data
 * @param array $updatedUser Updated user data
 * @return bool True if successful, false otherwise
 */
function updateUser($updatedUser) {
    if (empty($updatedUser) || !isset($updatedUser['id'])) {
        return false;
    }
    
    $users = readUsers();
    $updated = false;
    
    foreach ($users as $i => $user) {
        if (isset($user['id']) && $user['id'] === $updatedUser['id']) {
            $users[$i] = $updatedUser;
            $updated = true;
            break;
        }
    }
    
    if ($updated) {
        $content = '';
        foreach ($users as $user) {
            $content .= json_encode($user) . PHP_EOL;
        }
        return file_put_contents(USERS_FILE, $content) !== false;
    }
    
    return false;
}

/**
 * Logout user
 */
function logoutUser() {
    // Remove remember-me cookie if exists
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/', '', true, true);
    }
    
    // Clear session
    session_unset();
    session_destroy();
} 