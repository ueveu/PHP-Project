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
                            <th>Vorname</th>
                            <th>Nachname</th>
                            <th>Alias</th>
                            <th>E-Mail</th>
                            <th>Registriert am</th>
                            <th>Beiträge</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['firstname']); ?></td>
                                <td><?php echo htmlspecialchars($user['lastname']); ?></td>
                                <td><?php echo htmlspecialchars($user['alias']); ?></td>
                                <td><?php echo htmlspecialchars($user['email']); ?></td>
                                <td><?php echo date('d.m.Y', strtotime($user['created_at'])); ?></td>
                                <td><?php echo $postsPerUser[$user['id']] ?? 0; ?></td>
                                <td><?php echo $user['is_admin'] ? 'Admin' : 'Benutzer'; ?></td>
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

<style>
.admin-dashboard {
    padding: 2rem;
}

.stats-grid {
    display: grid;
    gap: 2rem;
    margin-top: 2rem;
}

.stat-box {
    background: #fff;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.table-container {
    overflow-x: auto;
    margin-top: 1rem;
}

.admin-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 800px;
}

.admin-table th,
.admin-table td {
    padding: 0.75rem;
    text-align: left;
    border-bottom: 1px solid #dee2e6;
}

.admin-table th {
    background-color: #f8f9fa;
    font-weight: 600;
    color: #495057;
}

.admin-table tr:hover {
    background-color: #f8f9fa;
}

.admin-table td {
    vertical-align: middle;
}

/* Column widths */
.admin-table th:nth-child(1), /* Vorname */
.admin-table td:nth-child(1) {
    width: 15%;
}

.admin-table th:nth-child(2), /* Nachname */
.admin-table td:nth-child(2) {
    width: 15%;
}

.admin-table th:nth-child(3), /* Alias */
.admin-table td:nth-child(3) {
    width: 10%;
}

.admin-table th:nth-child(4), /* E-Mail */
.admin-table td:nth-child(4) {
    width: 20%;
}

.admin-table th:nth-child(5), /* Registriert am */
.admin-table td:nth-child(5) {
    width: 15%;
}

.admin-table th:nth-child(6), /* Beiträge */
.admin-table td:nth-child(6) {
    width: 10%;
    text-align: center;
}

.admin-table th:nth-child(7), /* Status */
.admin-table td:nth-child(7) {
    width: 15%;
    text-align: center;
}

.admin-actions {
    margin-top: 1rem;
    display: flex;
    gap: 1rem;
}

.btn {
    display: inline-block;
    padding: 0.5rem 1rem;
    border-radius: 4px;
    text-decoration: none;
    font-weight: 500;
    cursor: pointer;
    border: none;
}

.btn-primary {
    background-color: #007bff;
    color: #fff;
}

.btn-primary:hover {
    background-color: #0056b3;
}
</style>

<?php
$content = ob_get_clean();
require 'templates/layout.php'; 