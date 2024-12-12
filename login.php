<?php
/**
 * Login page
 * Handles user authentication with server-side validation
 */

require_once 'includes/config.php';
require_once 'includes/user_functions.php';
require_once 'includes/validation.php';

// Redirect if already logged in
if (isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$errors = [];
$formData = [
    'username' => $_POST['username'] ?? '',
];

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    // Store username for form repopulation
    $formData['username'] = $username;
    
    // Validate username
    if (empty($username)) {
        $errors['username'] = 'Bitte geben Sie Ihren Benutzernamen ein.';
    } elseif (strlen($username) > 50) {
        $errors['username'] = 'Benutzername ist zu lang.';
    }
    
    // Validate password
    if (empty($password)) {
        $errors['password'] = 'Bitte geben Sie Ihr Passwort ein.';
    }
    
    // If no validation errors, attempt login
    if (empty($errors)) {
        $result = loginUser($username, $password, $remember);
        
        if ($result['success']) {
            showSuccess($result['message']);
            
            // Redirect to intended page or index
            $redirect = $_SESSION['redirect_after_login'] ?? 'index.php';
            unset($_SESSION['redirect_after_login']);
            header('Location: ' . $redirect);
            exit;
        } else {
            $errors['general'] = $result['message'];
        }
    }
}

$pageTitle = 'Login';
ob_start();
?>

<div class="auth-form">
    <h1>Login</h1>
    
    <?php if (!empty($errors['general'])): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($errors['general']); ?></div>
    <?php endif; ?>
    
    <form method="POST" action="" novalidate>
        <div class="form-group <?php echo !empty($errors['username']) ? 'has-error' : ''; ?>">
            <label for="username">Benutzername:</label>
            <input type="text" id="username" name="username" 
                   value="<?php echo htmlspecialchars($formData['username']); ?>">
            <?php if (!empty($errors['username'])): ?>
                <span class="error-message"><?php echo htmlspecialchars($errors['username']); ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group <?php echo !empty($errors['password']) ? 'has-error' : ''; ?>">
            <label for="password">Passwort:</label>
            <input type="password" id="password" name="password">
            <?php if (!empty($errors['password'])): ?>
                <span class="error-message"><?php echo htmlspecialchars($errors['password']); ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group remember-me">
            <label>
                <input type="checkbox" name="remember" <?php echo isset($_POST['remember']) ? 'checked' : ''; ?>>
                Angemeldet bleiben
            </label>
        </div>

        <button type="submit" class="btn btn-primary">Einloggen</button>
    </form>

    <p class="auth-links">
        Noch kein Konto? <a href="register.php">Jetzt registrieren</a>
    </p>
</div>

<style>
.has-error input {
    border-color: #dc3545;
}

.error-message {
    color: #dc3545;
    font-size: 0.875rem;
    margin-top: 0.25rem;
    display: block;
}

.auth-form {
    max-width: 400px;
    margin: 2rem auto;
    padding: 2rem;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.form-group {
    margin-bottom: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.form-group input[type="text"],
.form-group input[type="password"] {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 1rem;
}

.remember-me {
    display: flex;
    align-items: center;
}

.remember-me label {
    display: flex;
    align-items: center;
    margin: 0;
    cursor: pointer;
}

.remember-me input[type="checkbox"] {
    margin-right: 0.5rem;
}

.auth-links {
    text-align: center;
    margin-top: 1rem;
    padding-top: 1rem;
    border-top: 1px solid #dee2e6;
}
</style>

<?php
$content = ob_get_clean();
require 'templates/layout.php'; 