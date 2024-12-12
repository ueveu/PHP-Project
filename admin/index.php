<?php
/**
 * Admin Dashboard
 * Shows statistics and user management
 */

require_once '../includes/config.php';
require_once '../includes/user_functions.php';

// Ensure user is logged in and is admin
if (!isLoggedIn() || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit;
}

// Read users and contact messages
$users = readUsers();
$messages = [];
if (file_exists(DATA_PATH . 'contact_messages.txt')) {
    $messageLines = file(DATA_PATH . 'contact_messages.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($messageLines as $line) {
        $message = json_decode($line, true);
        if ($message) {
            $messages[] = $message;
        }
    }
}

// Read blog posts
$posts = [];
if (file_exists(POSTS_FILE)) {
    $postLines = file(POSTS_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($postLines as $line) {
        $post = json_decode($line, true);
        if ($post) {
            $posts[] = $post;
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
                <li>
                    <span>Anzahl Beiträge:</span>
                    <span><?php echo count($posts); ?></span>
                </li>
                <li>
                    <span>Anzahl Benutzer:</span>
                    <span><?php echo count($users); ?></span>
                </li>
                <li>
                    <span>Anzahl Nachrichten:</span>
                    <span><?php echo count($messages); ?></span>
                </li>
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
                            <th>Vorname</th>
                            <th>Nachname</th>
                            <th>Alias</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['firstname']); ?></td>
                                <td><?php echo htmlspecialchars($user['lastname']); ?></td>
                                <td><?php echo htmlspecialchars($user['alias']); ?></td>
                                <td><?php echo $user['is_admin'] ? 'Admin' : 'User'; ?></td>
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
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $recentMessages = array_slice(array_reverse($messages), 0, 5);
                        foreach ($recentMessages as $message): 
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($message['name']); ?></td>
                                <td><?php echo htmlspecialchars($message['email']); ?></td>
                                <td><?php echo htmlspecialchars($message['date']); ?></td>
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