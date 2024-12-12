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

    // Sanitize input data
    $title = trim(strip_tags($title));
    $content = trim($content);
    $firstName = trim(strip_tags($user['firstname'] ?? ''));
    $lastName = trim(strip_tags($user['lastname'] ?? ''));
    
    $post = [
        'id' => uniqid(),
        'title' => $title,
        'content' => $content,
        'author_id' => $userId,
        'author_firstname' => $firstName,
        'author_lastname' => $lastName,
        'created_at' => date('Y-m-d H:i:s'),
        'image_path' => $imagePath
    ];

    // Create posts directory if it doesn't exist
    if (!file_exists(dirname(POSTS_FILE))) {
        mkdir(dirname(POSTS_FILE), 0777, true);
    }

    // Read existing posts
    $posts = [];
    if (file_exists(POSTS_FILE) && filesize(POSTS_FILE) > 0) {
        $lines = file(POSTS_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (!empty(trim($line))) {
                $posts[] = $line;
            }
        }
    }

    // Add new post
    $posts[] = json_encode($post, JSON_UNESCAPED_UNICODE);

    // Save all posts back to file
    if (file_put_contents(POSTS_FILE, implode(PHP_EOL, array_filter($posts)) . PHP_EOL, LOCK_EX)) {
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
    if (!file_exists(POSTS_FILE)) {
        return [];
    }

    // Read and parse posts
    $posts = [];
    if (filesize(POSTS_FILE) > 0) {
        $lines = file(POSTS_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $postData = json_decode(trim($line), true);
            if ($postData !== null) {
                // Ensure all required fields exist
                if (isset($postData['title'], $postData['content'], 
                    $postData['author_firstname'], $postData['author_lastname'], 
                    $postData['created_at'])) {
                    $posts[] = $postData;
                }
            }
        }
    }

    // Sort by date (newest first)
    usort($posts, function($a, $b) {
        return strtotime($b['created_at']) - strtotime($a['created_at']);
    });

    // Apply pagination
    if ($limit !== null) {
        return array_slice($posts, $offset, $limit);
    }

    return $posts;
}

/**
 * Get a specific post by ID
 * @param string $postId The ID of the post to retrieve
 * @return array|null The post data or null if not found
 */
function getPostById($postId) {
    if (empty($postId)) {
        return null;
    }
    
    $posts = getAllPosts();
    foreach ($posts as $post) {
        if (isset($post['id']) && $post['id'] === $postId) {
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
    if (empty($userId)) {
        return [];
    }
    
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