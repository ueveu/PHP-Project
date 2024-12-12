<?php
/**
 * Registration page
 * Handles new user registration with server-side validation
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
    'email' => $_POST['email'] ?? '',
    'password' => '',
    'confirm_password' => ''
];

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Store form data for repopulating the form
    $formData['username'] = $username;
    $formData['email'] = $email;
    
    // Validate username
    $usernameValidation = validateUsername($username);
    if (!$usernameValidation['valid']) {
        $errors['username'] = $usernameValidation['message'];
    }
    
    // Validate email
    $emailValidation = validateEmail($email);
    if (!$emailValidation['valid']) {
        $errors['email'] = $emailValidation['message'];
    }
    
    // Validate password
    $passwordValidation = validatePassword($password);
    if (!$passwordValidation['valid']) {
        $errors['password'] = $passwordValidation['message'];
    }
    
    // Validate password confirmation
    if (empty($confirmPassword)) {
        $errors['confirm_password'] = 'Bitte bestätigen Sie Ihr Passwort.';
    } elseif ($password !== $confirmPassword) {
        $errors['confirm_password'] = 'Die Passwörter stimmen nicht überein.';
    }
    
    // Check if username already exists
    if (empty($errors['username']) && userExists($username)) {
        $errors['username'] = 'Dieser Benutzername ist bereits vergeben.';
    }
    
    // Check if email already exists
    if (empty($errors['email']) && emailExists($email)) {
        $errors['email'] = 'Diese E-Mail-Adresse wird bereits verwendet.';
    }
    
    // If no errors, proceed with registration
    if (empty($errors)) {
        $result = registerUser($username, $password, $email);
        
        if ($result['success']) {
            showSuccess($result['message']);
            header('Location: login.php');
            exit;
        } else {
            $errors['general'] = $result['message'];
        }
    }
}

$pageTitle = 'Registrierung';
ob_start();
?>

<div class="auth-form">
    <h1>Registrierung</h1>
    
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
            <small class="form-text">3-50 Zeichen, nur Buchstaben, Zahlen, Unterstriche und Bindestriche erlaubt.</small>
        </div>

        <div class="form-group <?php echo !empty($errors['email']) ? 'has-error' : ''; ?>">
            <label for="email">E-Mail:</label>
            <input type="text" id="email" name="email" 
                   value="<?php echo htmlspecialchars($formData['email']); ?>">
            <?php if (!empty($errors['email'])): ?>
                <span class="error-message"><?php echo htmlspecialchars($errors['email']); ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group <?php echo !empty($errors['password']) ? 'has-error' : ''; ?>">
            <label for="password">Passwort:</label>
            <input type="password" id="password" name="password">
            <?php if (!empty($errors['password'])): ?>
                <span class="error-message"><?php echo htmlspecialchars($errors['password']); ?></span>
            <?php endif; ?>
            <small class="form-text">Mindestens 8 Zeichen, ein Großbuchstabe, ein Kleinbuchstabe und eine Zahl.</small>
        </div>

        <div class="form-group <?php echo !empty($errors['confirm_password']) ? 'has-error' : ''; ?>">
            <label for="confirm_password">Passwort wiederholen:</label>
            <input type="password" id="confirm_password" name="confirm_password">
            <?php if (!empty($errors['confirm_password'])): ?>
                <span class="error-message"><?php echo htmlspecialchars($errors['confirm_password']); ?></span>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">Registrieren</button>
    </form>

    <p class="auth-links">
        Bereits registriert? <a href="login.php">Zum Login</a>
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

.form-text {
    color: #6c757d;
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

.form-group input {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 1rem;
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