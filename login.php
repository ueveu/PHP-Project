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
        <div class="form-group">
            <label for="alias">Alias:</label>
            <input type="text" id="alias" name="alias" 
                   value="<?php echo htmlspecialchars($formData['alias']); ?>"
                   data-validate="alias">
            <div class="validation-feedback"></div>
            <small class="form-text">Ihr Alias ist 4-8 Zeichen lang.</small>
        </div>

        <div class="form-group">
            <label for="password">Passwort:</label>
            <input type="password" id="password" name="password" data-validate="password">
            <div class="validation-feedback"></div>
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
    transition: border-color 0.2s ease-in-out;
}

.form-group input.is-valid {
    border-color: #28a745;
    background-color: #f8fff9;
}

.form-group input.is-invalid {
    border-color: #dc3545;
    background-color: #fff8f8;
}

.validation-feedback {
    font-size: 0.875rem;
    margin-top: 0.25rem;
    min-height: 20px;
}

.validation-feedback.is-valid {
    color: #28a745;
}

.validation-feedback.is-invalid {
    color: #dc3545;
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validation rules
    const validationRules = {
        alias: {
            minLength: 4,
            maxLength: 8,
            pattern: /^[a-zA-Z0-9_-]+$/,
            messages: {
                minLength: 'Alias muss mindestens 4 Zeichen lang sein.',
                maxLength: 'Alias darf maximal 8 Zeichen lang sein.',
                pattern: 'Alias darf nur Buchstaben, Zahlen, Unterstriche und Bindestriche enthalten.'
            }
        },
        password: {
            minLength: 6,
            messages: {
                minLength: 'Passwort muss mindestens 6 Zeichen lang sein.'
            }
        }
    };

    // Add input event listeners for live validation
    document.querySelectorAll('[data-validate]').forEach(input => {
        input.addEventListener('input', function() {
            validateField(this);
        });
    });

    function validateField(input) {
        const fieldType = input.dataset.validate;
        const rules = validationRules[fieldType];
        const value = input.value.trim();
        const feedbackElement = input.nextElementSibling;
        let isValid = true;
        let message = '';

        // Check minimum length
        if (rules.minLength && value.length < rules.minLength) {
            isValid = false;
            message = rules.messages.minLength;
        }
        // Check maximum length
        else if (rules.maxLength && value.length > rules.maxLength) {
            isValid = false;
            message = rules.messages.maxLength;
        }
        // Check pattern
        else if (rules.pattern && !rules.pattern.test(value)) {
            isValid = false;
            message = rules.messages.pattern;
        }

        // Update visual feedback
        input.classList.remove('is-valid', 'is-invalid');
        input.classList.add(isValid ? 'is-valid' : 'is-invalid');
        
        feedbackElement.classList.remove('is-valid', 'is-invalid');
        feedbackElement.classList.add(isValid ? 'is-valid' : 'is-invalid');
        feedbackElement.textContent = message;
    }
});
</script>

<?php
$content = ob_get_clean();
require 'templates/layout.php'; 