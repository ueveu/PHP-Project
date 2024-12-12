<?php
/**
 * Contact page
 * Simple contact form that saves messages to a file with validation
 */

require_once 'includes/config.php';
require_once 'includes/validation.php';

$errors = [];
$formData = [
    'name' => '',
    'email' => '',
    'message' => ''
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $message = $_POST['message'] ?? '';
    
    // Store form data for repopulation
    $formData['name'] = $name;
    $formData['email'] = $email;
    $formData['message'] = $message;
    
    // Validate name
    if (empty(trim($name))) {
        $errors['name'] = 'Bitte geben Sie Ihren Namen ein.';
    } elseif (strlen($name) > 100) {
        $errors['name'] = 'Name darf maximal 100 Zeichen lang sein.';
    }
    
    // Validate email
    if (empty(trim($email))) {
        $errors['email'] = 'Bitte geben Sie Ihre E-Mail-Adresse ein.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Bitte geben Sie eine gültige E-Mail-Adresse ein.';
    }
    
    // Validate message
    $messageValidation = validateContactMessage($message);
    if (!$messageValidation['valid']) {
        $errors['message'] = $messageValidation['message'];
    }
    
    // If no errors, save the message
    if (empty($errors)) {
        $contactMessage = [
            'name' => $name,
            'email' => $email,
            'message' => $message,
            'date' => date('Y-m-d H:i:s')
        ];
        
        $contactFile = DATA_PATH . 'contact_messages.txt';
        if (file_put_contents($contactFile, json_encode($contactMessage) . PHP_EOL, FILE_APPEND)) {
            showSuccess('Ihre Nachricht wurde erfolgreich gesendet!');
            // Clear form data after successful submission
            $formData = ['name' => '', 'email' => '', 'message' => ''];
        } else {
            $errors['general'] = 'Beim Senden Ihrer Nachricht ist ein Fehler aufgetreten.';
        }
    }
}

$pageTitle = 'Kontakt';
ob_start();
?>

<div class="contact">
    <h1>Kontakt</h1>
    
    <?php if (!empty($errors['general'])): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($errors['general']); ?></div>
    <?php endif; ?>
    
    <form method="POST" action="" class="contact-form" novalidate>
        <div class="form-group <?php echo !empty($errors['name']) ? 'has-error' : ''; ?>">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($formData['name']); ?>" required>
            <?php if (!empty($errors['name'])): ?>
                <span class="error-message"><?php echo htmlspecialchars($errors['name']); ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group <?php echo !empty($errors['email']) ? 'has-error' : ''; ?>">
            <label for="email">E-Mail:</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($formData['email']); ?>" required>
            <?php if (!empty($errors['email'])): ?>
                <span class="error-message"><?php echo htmlspecialchars($errors['email']); ?></span>
            <?php endif; ?>
        </div>

        <div class="form-group <?php echo !empty($errors['message']) ? 'has-error' : ''; ?>">
            <label for="message">Nachricht:</label>
            <textarea id="message" name="message" rows="5" required><?php echo htmlspecialchars($formData['message']); ?></textarea>
            <?php if (!empty($errors['message'])): ?>
                <span class="error-message"><?php echo htmlspecialchars($errors['message']); ?></span>
            <?php endif; ?>
            <small class="form-text">10-1000 Zeichen</small>
        </div>

        <button type="submit" class="btn btn-primary">Nachricht senden</button>
    </form>
    
    <div class="contact-info">
        <h2>Kontaktinformationen</h2>
        <p>Sie können uns auch direkt kontaktieren:</p>
        <ul>
            <li>E-Mail: info@example.com</li>
            <li>Telefon: +49 123 456789</li>
            <li>Adresse: Musterstraße 123, 12345 Musterstadt</li>
        </ul>
    </div>
</div>

<style>
.has-error input,
.has-error textarea {
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

.contact {
    max-width: 800px;
    margin: 2rem auto;
    padding: 2rem;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.contact-form {
    margin-bottom: 3rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 1rem;
}

.form-group textarea {
    resize: vertical;
    min-height: 120px;
}

.contact-info {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid #dee2e6;
}

.contact-info h2 {
    font-size: 1.5rem;
    margin-bottom: 1rem;
}

.contact-info ul {
    list-style: none;
    padding: 0;
}

.contact-info li {
    margin-bottom: 0.5rem;
}
</style>

<?php
$content = ob_get_clean();
require 'templates/layout.php'; 