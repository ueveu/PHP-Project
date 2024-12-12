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

$pageTitle = htmlentities($post['title'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
ob_start();
?>

<div class="post-detail">
    <article class="post">
        <h1><?php echo htmlentities($post['title'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></h1>
        
        <div class="post-meta">
            <span class="author">Von: <?php 
                $firstName = htmlentities($post['author_firstname'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
                $lastName = htmlentities($post['author_lastname'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
                echo $firstName && $lastName ? "$firstName $lastName" : 'Unbekannt';
            ?></span>
            <span class="date">am: <?php echo isset($post['created_at']) ? date('d.m.Y H:i', strtotime($post['created_at'])) : ''; ?></span>
        </div>
        
        <?php if (!empty($post['image_path'])): ?>
        <div class="post-image">
            <img src="<?php echo htmlentities($post['image_path'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?>" 
                 alt="Beitragsbild">
        </div>
        <?php endif; ?>
        
        <div class="post-content">
            <?php echo nl2br(htmlentities($post['content'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8')); ?>
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
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.post-detail h1 {
    font-size: 2rem;
    color: #2c3e50;
    margin-bottom: 1rem;
}

.post-meta {
    color: #666;
    margin-bottom: 1.5rem;
    font-size: 0.9rem;
}

.post-meta span {
    margin-right: 1rem;
}

.post-image {
    margin: 1rem 0;
}

.post-image img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
}

.post-content {
    line-height: 1.6;
    color: #333;
}

.post-navigation {
    margin-top: 2rem;
    text-align: center;
}

.btn-primary {
    display: inline-block;
    padding: 0.5rem 1rem;
    background: #3498db;
    color: white;
    text-decoration: none;
    border-radius: 4px;
    transition: background 0.3s;
}

.btn-primary:hover {
    background: #2980b9;
}
</style>

<?php
$content = ob_get_clean();
require 'templates/layout.php'; 