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
    'firstname' => $_POST['firstname'] ?? '',
    'lastname' => $_POST['lastname'] ?? '',
    'alias' => $_POST['alias'] ?? '',
    'email' => $_POST['email'] ?? '',
    'password' => '',
    'confirm_password' => ''
];

// Handle registration form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $alias = trim($_POST['alias'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    // Store form data for repopulating the form
    $formData['firstname'] = $firstname;
    $formData['lastname'] = $lastname;
    $formData['alias'] = $alias;
    $formData['email'] = $email;
    
    // Validate firstname
    $firstnameValidation = validateFirstname($firstname);
    if (!$firstnameValidation['valid']) {
        $errors['firstname'] = $firstnameValidation['message'];
    }
    
    // Validate lastname
    $lastnameValidation = validateLastname($lastname);
    if (!$lastnameValidation['valid']) {
        $errors['lastname'] = $lastnameValidation['message'];
    }
    
    // Validate alias
    $aliasValidation = validateAlias($alias);
    if (!$aliasValidation['valid']) {
        $errors['alias'] = $aliasValidation['message'];
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
    
    // Check if alias already exists
    if (empty($errors['alias']) && aliasExists($alias)) {
        $errors['alias'] = 'Dieser Alias ist bereits vergeben.';
    }
    
    // Check if email already exists
    if (empty($errors['email']) && emailExists($email)) {
        $errors['email'] = 'Diese E-Mail-Adresse wird bereits verwendet.';
    }
    
    // If no errors, proceed with registration
    if (empty($errors)) {
        $result = registerUser($firstname, $lastname, $alias, $password, $email);
        
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
        <div class="form-group">
            <label for="firstname">Vorname:</label>
            <input type="text" id="firstname" name="firstname" 
                   value="<?php echo htmlspecialchars($formData['firstname'] ?? ''); ?>"
                   data-validate="firstname">
            <div class="validation-feedback"></div>
            <small class="form-text">3-20 Zeichen, nur Buchstaben und Bindestriche erlaubt.</small>
        </div>

        <div class="form-group">
            <label for="lastname">Nachname:</label>
            <input type="text" id="lastname" name="lastname" 
                   value="<?php echo htmlspecialchars($formData['lastname'] ?? ''); ?>"
                   data-validate="lastname">
            <div class="validation-feedback"></div>
            <small class="form-text">3-20 Zeichen, nur Buchstaben und Bindestriche erlaubt.</small>
        </div>

        <div class="form-group">
            <label for="alias">Alias:</label>
            <input type="text" id="alias" name="alias" 
                   value="<?php echo htmlspecialchars($formData['alias'] ?? ''); ?>"
                   data-validate="alias">
            <div class="validation-feedback"></div>
            <small class="form-text">4-8 Zeichen, nur Buchstaben, Zahlen, Unterstriche und Bindestriche erlaubt.</small>
        </div>

        <div class="form-group">
            <label for="email">E-Mail:</label>
            <input type="email" id="email" name="email" 
                   value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>"
                   data-validate="email">
            <div class="validation-feedback"></div>
        </div>

        <div class="form-group">
            <label for="password">Passwort:</label>
            <input type="password" id="password" name="password" data-validate="password">
            <div class="validation-feedback"></div>
            <small class="form-text">Mindestens 6 Zeichen, ein Großbuchstabe, ein Kleinbuchstabe, eine Zahl und ein Sonderzeichen.</small>
        </div>

        <div class="form-group">
            <label for="password_confirm">Passwort wiederholen:</label>
            <input type="password" id="password_confirm" name="password_confirm" data-validate="password_confirm">
            <div class="validation-feedback"></div>
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
.form-group input[type="email"],
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
        firstname: {
            minLength: 3,
            maxLength: 20,
            pattern: /^[a-zA-ZäöüÄÖÜß-]+$/,
            messages: {
                minLength: 'Vorname muss mindestens 3 Zeichen lang sein.',
                maxLength: 'Vorname darf maximal 20 Zeichen lang sein.',
                pattern: 'Vorname darf nur Buchstaben und Bindestriche enthalten.'
            }
        },
        lastname: {
            minLength: 3,
            maxLength: 20,
            pattern: /^[a-zA-ZäöüÄÖÜß-]+$/,
            messages: {
                minLength: 'Nachname muss mindestens 3 Zeichen lang sein.',
                maxLength: 'Nachname darf maximal 20 Zeichen lang sein.',
                pattern: 'Nachname darf nur Buchstaben und Bindestriche enthalten.'
            }
        },
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
        email: {
            pattern: /^[^@\s]+@[^@\s]+\.[^@\s]+$/,
            messages: {
                pattern: 'Bitte geben Sie eine gültige E-Mail-Adresse ein.'
            }
        },
        password: {
            minLength: 6,
            pattern: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^a-zA-Z\d]).+$/,
            messages: {
                minLength: 'Passwort muss mindestens 6 Zeichen lang sein.',
                pattern: 'Passwort muss mindestens einen Großbuchstaben, einen Kleinbuchstaben, eine Zahl und ein Sonderzeichen enthalten.'
            }
        },
        password_confirm: {
            matchWith: 'password',
            messages: {
                matchWith: 'Passwörter stimmen nicht überein.'
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
        // Check password confirmation
        else if (rules.matchWith) {
            const originalPassword = document.getElementById(rules.matchWith).value;
            if (value !== originalPassword) {
                isValid = false;
                message = rules.messages.matchWith;
            }
        }

        // Update visual feedback
        input.classList.remove('is-valid', 'is-invalid');
        input.classList.add(isValid ? 'is-valid' : 'is-invalid');
        
        feedbackElement.classList.remove('is-valid', 'is-invalid');
        feedbackElement.classList.add(isValid ? 'is-valid' : 'is-invalid');
        feedbackElement.textContent = message;

        // Special handling for password confirmation
        if (fieldType === 'password') {
            const confirmInput = document.getElementById('password_confirm');
            if (confirmInput && confirmInput.value) {
                validateField(confirmInput);
            }
        }
    }
});
</script>

<?php
$content = ob_get_clean();
require 'templates/layout.php'; 