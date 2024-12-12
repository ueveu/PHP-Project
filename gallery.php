<?php
/**
 * Gallery page
 * Displays images uploaded by users
 */

require_once 'includes/config.php';

// Handle image upload
$uploadError = null;
$uploadSuccess = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isLoggedIn()) {
    if (isset($_FILES['image'])) {
        $file = $_FILES['image'];
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $maxSize = 5 * 1024 * 1024; // 5MB
        
        if (!in_array($file['type'], $allowedTypes)) {
            $uploadError = 'Nur JPEG, PNG und GIF Dateien sind erlaubt.';
        } elseif ($file['size'] > $maxSize) {
            $uploadError = 'Die Datei ist zu groß. Maximale Größe ist 5MB.';
        } else {
            $uploadDir = ROOT_PATH . 'assets/images/gallery/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $filename = uniqid() . '_' . basename($file['name']);
            $destination = $uploadDir . $filename;
            
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                // Save image info to file
                $imageInfo = [
                    'filename' => $filename,
                    'uploaded_by' => isset($_SESSION['username']) ? $_SESSION['username'] : 'anonymous',
                    'upload_date' => date('Y-m-d H:i:s')
                ];
                
                $galleryFile = DATA_PATH . 'gallery.txt';
                if (!file_exists($galleryFile)) {
                    touch($galleryFile);
                }
                file_put_contents($galleryFile, json_encode($imageInfo) . PHP_EOL, FILE_APPEND);
                
                $uploadSuccess = true;
                showSuccess('Bild wurde erfolgreich hochgeladen!');
            } else {
                $uploadError = 'Fehler beim Hochladen der Datei.';
            }
        }
    }
}

// Get all images
$images = [];
$galleryFile = DATA_PATH . 'gallery.txt';
if (file_exists($galleryFile) && filesize($galleryFile) > 0) {
    $lines = file($galleryFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($lines) {
        foreach ($lines as $line) {
            $imageData = json_decode($line, true);
            if ($imageData) {
                $images[] = $imageData;
            }
        }
    }
}

// Sort images by upload date (newest first)
if (!empty($images)) {
    usort($images, function($a, $b) {
        return isset($a['upload_date'], $b['upload_date']) 
            ? strtotime($b['upload_date']) - strtotime($a['upload_date'])
            : 0;
    });
}

$pageTitle = 'Bildergalerie';
ob_start();
?>

<div class="gallery">
    <h1>Bildergalerie</h1>
    
    <?php if (isLoggedIn()): ?>
        <?php if ($uploadError): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($uploadError); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="" enctype="multipart/form-data" class="upload-form">
            <div class="form-group">
                <label for="image">Bild auswählen:</label>
                <input type="file" id="image" name="image" accept="image/jpeg,image/png,image/gif" required>
            </div>
            <button type="submit" class="btn btn-primary">Hochladen</button>
        </form>
    <?php endif; ?>
    
    <div class="gallery-grid">
        <?php if (empty($images)): ?>
            <p class="no-images">Noch keine Bilder in der Galerie.</p>
        <?php else: ?>
            <?php foreach ($images as $image): ?>
                <div class="gallery-item">
                    <img src="assets/images/gallery/<?php echo htmlspecialchars($image['filename'] ?? ''); ?>" 
                         alt="Galeriebild">
                    <div class="image-info">
                        <span class="uploader">Von: <?php echo htmlspecialchars($image['uploaded_by'] ?? 'Unbekannt'); ?></span>
                        <span class="date">Am: <?php echo isset($image['upload_date']) ? date('d.m.Y', strtotime($image['upload_date'])) : ''; ?></span>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<?php
$content = ob_get_clean();
require 'templates/layout.php'; 