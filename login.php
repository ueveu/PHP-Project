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
    'alias' => $_POST['alias'] ?? '',
];

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $alias = trim($_POST['alias'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    // Store alias for form repopulation
    $formData['alias'] = $alias;
    
    // Validate alias
    if (empty($alias)) {
        $errors['alias'] = 'Bitte geben Sie Ihren Alias ein.';
    } elseif (strlen($alias) < 4 || strlen($alias) > 8) {
        $errors['alias'] = 'Alias muss zwischen 4 und 8 Zeichen lang sein.';
    }
    
    // Validate password
    if (empty($password)) {
        $errors['password'] = 'Bitte geben Sie Ihr Passwort ein.';
    }
    
    // If no validation errors, attempt login
    if (empty($errors)) {
        $result = loginUser($alias, $password, $remember);
        
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
        <div class="form-group <?php echo !empty($errors['alias']) ? 'has-error' : ''; ?>">
            <label for="alias">Alias:</label>
            <input type="text" id="alias" name="alias" 
                   value="<?php echo htmlspecialchars($formData['alias']); ?>">
            <?php if (!empty($errors['alias'])): ?>
                <span class="error-message"><?php echo htmlspecialchars($errors['alias']); ?></span>
            <?php endif; ?>
            <small class="form-text">Ihr Alias ist 4-8 Zeichen lang.</small>
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

.form-text {
    color: #6c757d;
    font-size: 0.875rem;
    margin-top: 0.25rem;
    display: block;
}

.auth-form {
    max-width: 500px;
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
    white-space: nowrap;
}

.remember-me label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.remember-me input[type="checkbox"] {
    margin: 0;
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