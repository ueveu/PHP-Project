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
    $users = [];
    if (file_exists(USERS_FILE)) {
        $lines = file(USERS_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines) {
            foreach ($lines as $line) {
                $user = json_decode($line, true);
                if ($user) {
                    $users[] = $user;
                }
            }
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
    $users = readUsers();
    foreach ($users as $user) {
        if ($user['alias'] === $alias) {
            return true;
        }
    }
    return false;
}

/**
 * Register a new user
 * @param string $firstname User's first name
 * @param string $lastname User's last name
 * @param string $alias User's alias
 * @param string $password User's password
 * @return array ['success' => bool, 'message' => string]
 */
function registerUser($firstname, $lastname, $alias, $password) {
    // Additional validation
    if (aliasExists($alias)) {
        return ['success' => false, 'message' => 'Dieser Alias ist bereits vergeben.'];
    }
    
    // Create user data
    $user = [
        'id' => uniqid(),
        'firstname' => $firstname,
        'lastname' => $lastname,
        'alias' => $alias,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'created_at' => date('Y-m-d H:i:s'),
        'is_admin' => false // Default to non-admin
    ];
    
    // Make first user an admin
    $users = readUsers();
    if (empty($users)) {
        $user['is_admin'] = true;
    }
    
    // Ensure data directory exists
    if (!file_exists(DATA_PATH)) {
        mkdir(DATA_PATH, 0777, true);
    }
    
    // Ensure users file exists
    if (!file_exists(USERS_FILE)) {
        file_put_contents(USERS_FILE, '');
    }
    
    // Save user to file
    $success = file_put_contents(USERS_FILE, json_encode($user) . PHP_EOL, FILE_APPEND);
    
    if ($success !== false) {
        return ['success' => true, 'message' => 'Registrierung erfolgreich! Sie können sich jetzt einloggen.'];
    } else {
        error_log("Failed to write to users file: " . USERS_FILE);
        return ['success' => false, 'message' => 'Fehler bei der Registrierung. Bitte versuchen Sie es später erneut.'];
    }
}

/**
 * Get user by ID
 * @param string $userId User ID to find
 * @return array|null User data or null if not found
 */
function getUserById($userId) {
    $users = readUsers();
    foreach ($users as $user) {
        if ($user['id'] === $userId) {
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
    $users = readUsers();
    foreach ($users as $user) {
        if ($user['alias'] === $alias) {
            return $user;
        }
    }
    return null;
}

/**
 * Login user
 * @param string $alias User's alias
 * @param string $password User's password
 * @param bool $remember Whether to set remember cookie
 * @return array ['success' => bool, 'message' => string]
 */
function loginUser($alias, $password, $remember = false) {
    $users = readUsers();
    foreach ($users as $user) {
        if ($user['alias'] === $alias) {
            if (password_verify($password, $user['password'])) {
                // Set session
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['alias'] = $user['alias'];
                $_SESSION['is_admin'] = $user['is_admin'];
                
                // Set remember cookie if requested
                if ($remember) {
                    $token = bin2hex(random_bytes(32));
                    setcookie('remember_token', $token, time() + (86400 * 30), '/'); // 30 days
                }
                
                return ['success' => true, 'message' => 'Login erfolgreich!'];
            }
            return ['success' => false, 'message' => 'Falsches Passwort.'];
        }
    }
    return ['success' => false, 'message' => 'Benutzer nicht gefunden.'];
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