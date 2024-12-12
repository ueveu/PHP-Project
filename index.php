<?php
/**
 * Main index page
 * Displays all blog posts in reverse chronological order
 */

require_once 'includes/config.php';
require_once 'includes/post_functions.php';

// Get current page for pagination
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$offset = ($page - 1) * ITEMS_PER_PAGE;

// Get posts for current page
$posts = getAllPosts(ITEMS_PER_PAGE, $offset);
$totalPosts = countTotalPosts();
$totalPages = ceil($totalPosts / ITEMS_PER_PAGE);

// Set page title
$pageTitle = 'Blog Übersicht';

// Start output buffering
ob_start();
?>

<div class="blog-posts">
    <h1>Aktuelle Blog-Beiträge</h1>
    
    <?php if (empty($posts)): ?>
        <p class="no-posts">Noch keine Beiträge vorhanden.</p>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <article class="post">
                <h2><?php echo htmlentities($post['title'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></h2>
                <div class="post-meta">
                    <span class="author">Von: <?php echo htmlentities($post['author_name'] ?? 'Unbekannt', ENT_QUOTES | ENT_HTML5, 'UTF-8'); ?></span>
                    <span class="date">am: <?php echo isset($post['created_at']) ? date('d.m.Y H:i', strtotime($post['created_at'])) : ''; ?></span>
                </div>
                <div class="post-content">
                    <?php 
                    if (isset($post['content'])) {
                        // Show first 200 characters of content with ellipsis
                        $content = strip_tags($post['content']);
                        $excerpt = mb_substr($content, 0, 200, 'UTF-8');
                        echo htmlentities($excerpt, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                        echo (mb_strlen($content, 'UTF-8') > 200) ? '...' : '';
                    }
                    ?>
                </div>
                <?php if (isset($post['id'])): ?>
                    <a href="post.php?id=<?php echo urlencode($post['id']); ?>" class="read-more">Weiterlesen</a>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>

        <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <?php if ($page > 1): ?>
                    <a href="?page=<?php echo ($page - 1); ?>" class="prev">&laquo; Vorherige</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <?php if ($i == $page): ?>
                        <span class="current"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($page < $totalPages): ?>
                    <a href="?page=<?php echo ($page + 1); ?>" class="next">Nächste &raquo;</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php
$content = ob_get_clean();
require 'templates/layout.php'; 