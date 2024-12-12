<?php
/**
 * Create Post page
 * Allows logged-in users to create new blog posts with validation
 */

require_once 'includes/config.php';
require_once 'includes/post_functions.php';
require_once 'includes/validation.php';

// Redirect if not logged in
if (!isLoggedIn()) {
    $_SESSION['redirect_after_login'] = 'create-post.php';
    showError('Sie müssen eingeloggt sein, um einen Beitrag zu erstellen.');
    header('Location: login.php');
    exit;
}

$errors = [];
$formData = [
    'title' => '',
    'content' => ''
];

// Handle post creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'] ?? '';
    $content = $_POST['content'] ?? '';
    $image = $_FILES['image'] ?? null;
    
    // Store form data for repopulation
    $formData['title'] = $title;
    $formData['content'] = $content;
    
    // Validate title
    $titleValidation = validatePostTitle($title);
    if (!$titleValidation['valid']) {
        $errors['title'] = $titleValidation['message'];
    }
    
    // Validate content
    $contentValidation = validatePostContent($content);
    if (!$contentValidation['valid']) {
        $errors['content'] = $contentValidation['message'];
    }
    
    // Handle image upload if present
    $imagePath = '';
    if ($image && $image['error'] === UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($image['type'], $allowedTypes)) {
            $errors['image'] = 'Nur JPEG, PNG und GIF Dateien sind erlaubt.';
        } elseif ($image['size'] > $maxSize) {
            $errors['image'] = 'Die Datei ist zu groß. Maximale Größe ist 5MB.';
        } else {
            $uploadDir = ROOT_PATH . 'assets/images/posts/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $filename = uniqid() . '_' . basename($image['name']);
            $destination = $uploadDir . $filename;
            
            if (move_uploaded_file($image['tmp_name'], $destination)) {
                $imagePath = 'assets/images/posts/' . $filename;
            } else {
                $errors['image'] = 'Fehler beim Hochladen des Bildes.';
            }
        }
    }
    
    // If no errors, create the post
    if (empty($errors)) {
        $result = createPost($title, $content, $_SESSION['user_id'], $imagePath);
        
        if ($result['success']) {
            showSuccess($result['message']);
            header('Location: index.php');
            exit;
        } else {
            $errors['general'] = $result['message'];
        }
    }
}

$pageTitle = 'Neuer Beitrag';
ob_start();
?>

<div class="create-post">
    <h1>Neuen Beitrag erstellen</h1>
    
    <?php if (!empty($errors['general'])): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($errors['general']); ?></div>
    <?php endif; ?>
    
    <form method="POST" action="" enctype="multipart/form-data" novalidate>
        <div class="form-group <?php echo !empty($errors['title']) ? 'has-error' : ''; ?>">
            <label for="title">Titel:</label>
            <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($formData['title']); ?>" required>
            <?php if (!empty($errors['title'])): ?>
                <span class="error-message"><?php echo htmlspecialchars($errors['title']); ?></span>
            <?php endif; ?>
            <small class="form-text">3-255 Zeichen</small>
        </div>

        <div class="form-group <?php echo !empty($errors['content']) ? 'has-error' : ''; ?>">
            <label for="content">Inhalt:</label>
            <textarea id="content" name="content" rows="10" required><?php echo htmlspecialchars($formData['content']); ?></textarea>
            <?php if (!empty($errors['content'])): ?>
                <span class="error-message"><?php echo htmlspecialchars($errors['content']); ?></span>
            <?php endif; ?>
            <small class="form-text">Mindestens 10 Zeichen</small>
        </div>

        <div class="form-group <?php echo !empty($errors['image']) ? 'has-error' : ''; ?>">
            <label for="image">Bild (optional):</label>
            <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif">
            <?php if (!empty($errors['image'])): ?>
                <span class="error-message"><?php echo htmlspecialchars($errors['image']); ?></span>
            <?php endif; ?>
            <small class="form-text">Erlaubte Formate: JPEG, PNG, GIF. Maximale Größe: 5MB</small>
        </div>

        <button type="submit" class="btn btn-primary">Beitrag veröffentlichen</button>
    </form>
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

.create-post {
    max-width: 800px;
    margin: 2rem auto;
    padding: 2rem;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.form-group input[type="text"],
.form-group textarea {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ced4da;
    border-radius: 4px;
    font-size: 1rem;
}

.form-group textarea {
    resize: vertical;
    min-height: 200px;
}

.form-group input[type="file"] {
    display: block;
    margin-top: 0.5rem;
}
</style>

<?php
$content = ob_get_clean();
require 'templates/layout.php'; 