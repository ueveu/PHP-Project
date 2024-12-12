<?php
/**
 * Admin Dashboard
 * Overview of system statistics and user management
 */

require_once '../includes/config.php';
require_once '../includes/user_functions.php';

// Ensure user is logged in and is admin
if (!isLoggedIn() || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../login.php');
    exit;
}

// Get statistics
$users = readUsers();
$posts = file_exists(POSTS_FILE) ? file(POSTS_FILE, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
$messages = file_exists(DATA_PATH . 'contact_messages.txt') ? 
    file(DATA_PATH . 'contact_messages.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];

$pageTitle = 'Admin Dashboard';
ob_start();
?>

<div class="admin-dashboard">
    <h1>Admin Dashboard</h1>

    <div class="stats-grid">
        <!-- Statistics Box -->
        <div class="stat-box">
            <h3>Gesamtstatistik</h3>
            <ul>
                <li>Anzahl Beiträge: <?php echo count($posts); ?></li>
                <li>Anzahl Benutzer: <?php echo count($users); ?></li>
                <li>Anzahl Nachrichten: <?php echo count($messages); ?></li>
            </ul>
            <div class="admin-actions">
                <a href="maintenance.php" class="btn btn-primary">System Wartung</a>
            </div>
        </div>

        <!-- Users Overview Box -->
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
                            <?php $userData = json_decode($user, true); ?>
                            <tr>
                                <td><?php echo htmlspecialchars($userData['firstname']); ?></td>
                                <td><?php echo htmlspecialchars($userData['lastname']); ?></td>
                                <td><?php echo htmlspecialchars($userData['alias']); ?></td>
                                <td><?php echo $userData['is_admin'] ? 'Admin' : 'User'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Latest Messages Box -->
        <div class="stat-box">
            <h3>Letzte Kontaktanfragen</h3>
            <div class="table-container">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Nachricht</th>
                            <th>Datum</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $latestMessages = array_slice(array_reverse($messages), 0, 5);
                        foreach ($latestMessages as $message):
                            $messageData = json_decode($message, true);
                            if ($messageData):
                        ?>
                            <tr>
                                <td><?php echo htmlspecialchars($messageData['name']); ?></td>
                                <td><?php echo htmlspecialchars(substr($messageData['message'], 0, 50)) . '...'; ?></td>
                                <td><?php echo htmlspecialchars($messageData['date']); ?></td>
                            </tr>
                        <?php 
                            endif;
                        endforeach; 
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$content = ob_get_clean();
require 'templates/layout.php'; 