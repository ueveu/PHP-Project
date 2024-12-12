<?php
/**
 * Blog post management functions
 * Handles creating, reading, and managing blog posts
 */

require_once 'config.php';
require_once 'user_functions.php';

/**
 * Create a new blog post
 * @param string $title Post title
 * @param string $content Post content
 * @param string $userId User ID of the author
 * @param string $imagePath Optional path to attached image
 * @return array Result with status and message
 */
function createPost($title, $content, $userId, $imagePath = '') {
    if (!isLoggedIn()) {
        return ['success' => false, 'message' => 'Sie müssen eingeloggt sein, um einen Beitrag zu erstellen.'];
    }

    if (empty($title) || empty($content)) {
        return ['success' => false, 'message' => 'Titel und Inhalt sind erforderlich.'];
    }

    $user = getUserById($userId);
    if (!$user) {
        return ['success' => false, 'message' => 'Benutzer nicht gefunden.'];
    }

    $post = [
        'id' => uniqid(),
        'title' => $title,
        'content' => $content,
        'author_id' => $userId,
        'author_name' => $user['username'],
        'created_at' => date('Y-m-d H:i:s'),
        'image_path' => $imagePath
    ];

    // Read existing posts
    $posts = [];
    if (file_exists(POSTS_FILE) && filesize(POSTS_FILE) > 0) {
        $posts = array_filter(
            file(POSTS_FILE, FILE_IGNORE_NEW_LINES),
            function($line) { return !empty(trim($line)); }
        );
    }

    // Add new post
    $posts[] = json_encode($post);

    // Save all posts back to file
    if (file_put_contents(POSTS_FILE, implode(PHP_EOL, $posts) . PHP_EOL)) {
        return ['success' => true, 'message' => 'Beitrag erfolgreich erstellt!'];
    } else {
        return ['success' => false, 'message' => 'Fehler beim Speichern des Beitrags.'];
    }
}

/**
 * Get all blog posts
 * @param int $limit Optional limit of posts to return
 * @param int $offset Optional offset for pagination
 * @return array Array of posts
 */
function getAllPosts($limit = null, $offset = 0) {
    if (!file_exists(POSTS_FILE) || filesize(POSTS_FILE) === 0) {
        return [];
    }

    // Read and filter out empty lines
    $posts = array_filter(
        file(POSTS_FILE, FILE_IGNORE_NEW_LINES),
        function($line) { return !empty(trim($line)); }
    );

    $validPosts = [];
    foreach ($posts as $post) {
        $postData = json_decode(trim($post), true);
        if ($postData !== null) {
            $validPosts[] = $postData;
        }
    }

    // Sort by date (newest first)
    usort($validPosts, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });

    if ($limit !== null) {
        return array_slice($validPosts, $offset, $limit);
    }

    return $validPosts;
}

/**
 * Get a specific post by ID
 * @param string $postId The ID of the post to retrieve
 * @return array|null The post data or null if not found
 */
function getPostById($postId) {
    $posts = getAllPosts();
    foreach ($posts as $post) {
        if ($post['id'] === $postId) {
            return $post;
        }
    }
    return null;
}

/**
 * Get posts by user ID
 * @param string $userId User ID
 * @return array Array of posts
 */
function getPostsByUser($userId) {
    $allPosts = getAllPosts();
    return array_filter($allPosts, function($post) use ($userId) {
        return isset($post['author_id']) && $post['author_id'] === $userId;
    });
}

/**
 * Count total number of posts
 * @return int Total number of posts
 */
function countTotalPosts() {
    return count(getAllPosts());
} 