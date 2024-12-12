<?php
/**
 * Admin Dashboard
 * Shows statistics and user management
 */

require_once '../includes/config.php';
require_once '../includes/user_functions.php';
require_once '../includes/post_functions.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../index.php');
    exit;
}

// Get statistics
$totalPosts = countTotalPosts();

// Get all users
$users = [];
$usersFile = USERS_FILE;
if (file_exists($usersFile)) {
    $lines = file($usersFile, FILE_IGNORE_NEW_LINES);
    foreach ($lines as $line) {
        $userData = json_decode($line, true);
        if ($userData) {
            $users[] = $userData;
        }
    }
}

// Get posts per user
$postsPerUser = [];
$posts = getAllPosts();
foreach ($posts as $post) {
    $authorId = $post['author_id'];
    if (!isset($postsPerUser[$authorId])) {
        $postsPerUser[$authorId] = 0;
    }
    $postsPerUser[$authorId]++;
}

// Get contact messages
$messages = [];
$contactFile = DATA_PATH . 'contact_messages.txt';
if (file_exists($contactFile)) {
    $lines = file($contactFile, FILE_IGNORE_NEW_LINES);
    foreach ($lines as $line) {
        $messageData = json_decode($line, true);
        if ($messageData) {
            $messages[] = $messageData;
        }
    }
}

$pageTitle = 'Admin Dashboard';
ob_start();
?>

<div class="admin-dashboard">
    <h1>Admin Dashboard</h1>
    
    <div class="stats-grid">
        <div class="stat-box">
            <h3>Gesamtstatistik</h3>
            <ul>
                <li>Anzahl Beiträge: <?php echo $totalPosts; ?></li>
                <li>Anzahl Benutzer: <?php echo count($users); ?></li>
                <li>Anzahl Nachrichten: <?php echo count($messages); ?></li>
            </ul>
            <div class="admin-actions">
                <a href="maintenance.php" class="btn btn-primary">System Wartung</a>
            </div>
        </div>
        
        <div class="stat-box">
            <h3>Benutzerübersicht</h3>
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Benutzername</th>
                            <th>E-Mail</th>
                            <th>Registriert am</th>
                            <th>Beiträge</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo date('d.m.Y', strtotime($user['created_at'])); ?></td>
                                <td><?php echo $postsPerUser[$user['id']] ?? 0; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <div class="stat-box">
            <h3>Letzte Kontaktanfragen</h3>
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>E-Mail</th>
                            <th>Datum</th>
                            <th>Nachricht</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice(array_reverse($messages), 0, 5) as $message): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($message['name']); ?></td>
                                <td><?php echo htmlspecialchars($message['email']); ?></td>
                                <td><?php echo date('d.m.Y', strtotime($message['date'])); ?></td>
                                <td><?php echo htmlspecialchars(substr($message['message'], 0, 50)) . '...'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require 'templates/layout.php'; 