<?php
/**
 * Post detail page
 * Shows full blog post content
 */

require_once 'includes/config.php';
require_once 'includes/post_functions.php';

// Get post ID from URL
$postId = $_GET['id'] ?? null;

if (!$postId) {
    showError('Kein Beitrag ausgewählt.');
    header('Location: index.php');
    exit;
}

// Get post data
$post = getPostById($postId);

if (!$post) {
    showError('Beitrag nicht gefunden.');
    header('Location: index.php');
    exit;
}

$pageTitle = $post['title'];
ob_start();
?>

<div class="post-detail">
    <article class="post">
        <h1><?php echo htmlspecialchars($post['title']); ?></h1>
        
        <div class="post-meta">
            <span class="author">Von <?php echo htmlspecialchars($post['author_name']); ?></span>
            <span class="date">am <?php echo date('d.m.Y H:i', strtotime($post['created_at'])); ?></span>
        </div>
        
        <?php if (!empty($post['image_path'])): ?>
        <div class="post-image">
            <img src="<?php echo htmlspecialchars($post['image_path']); ?>" alt="Beitragsbild">
        </div>
        <?php endif; ?>
        
        <div class="post-content">
            <?php echo nl2br(htmlspecialchars($post['content'])); ?>
        </div>
    </article>
    
    <div class="post-navigation">
        <a href="index.php" class="btn btn-primary">&laquo; Zurück zur Übersicht</a>
    </div>
</div>

<style>
/* Additional styles for post detail page */
.post-detail {
    max-width: 800px;
    margin: 0 auto;
}

.post-detail .post {
    padding: 2rem;
}

.post-detail h1 {
    font-size: 2rem;
    color: #2c3e50;
    margin-bottom: 1rem;
}

.post-image {
    margin: 1rem 0;
}

.post-image img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
}

.post-navigation {
    margin-top: 2rem;
    text-align: center;
}
</style>

<?php
$content = ob_get_clean();
require 'templates/layout.php'; 