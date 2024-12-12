<?php
/**
 * System Maintenance Tool
 * Backend-only application for system maintenance
 * Only accessible by admins
 */

require_once '../includes/config.php';
require_once '../includes/user_functions.php';

// Check if user is logged in and is admin
if (!isLoggedIn() || !isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: ../index.php');
    exit;
}

// Handle maintenance actions
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'clear_logs':
            // Clear log files
            $logFiles = glob(DATA_PATH . '*.log');
            foreach ($logFiles as $file) {
                file_put_contents($file, '');
            }
            $message = 'Log-Dateien wurden erfolgreich geleert.';
            break;
            
        case 'optimize_data':
            // Remove invalid entries from data files
            $files = ['posts.txt', 'gallery.txt', 'contact_messages.txt'];
            foreach ($files as $file) {
                $path = DATA_PATH . $file;
                if (file_exists($path)) {
                    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                    $validLines = [];
                    foreach ($lines as $line) {
                        if (json_decode($line) !== null) {
                            $validLines[] = $line;
                        }
                    }
                    file_put_contents($path, implode(PHP_EOL, $validLines));
                }
            }
            $message = 'Daten wurden erfolgreich optimiert.';
            break;
            
        case 'system_info':
            // Generate system info report
            $info = [
                'PHP Version' => PHP_VERSION,
                'Server Software' => $_SERVER['SERVER_SOFTWARE'],
                'Database Type' => 'Text Files',
                'Total Posts' => count(file(DATA_PATH . 'posts.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)),
                'Total Users' => count(file(DATA_PATH . 'users.txt', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES)),
                'Disk Space Used' => formatBytes(getFolderSize(DATA_PATH))
            ];
            $reportPath = DATA_PATH . 'system_report_' . date('Y-m-d_H-i-s') . '.txt';
            file_put_contents($reportPath, json_encode($info, JSON_PRETTY_PRINT));
            $message = 'System-Report wurde erstellt: ' . basename($reportPath);
            break;
    }
}

// Helper function to format bytes
function formatBytes($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    return round($bytes / pow(1024, $pow), 2) . ' ' . $units[$pow];
}

// Helper function to get folder size
function getFolderSize($path) {
    $size = 0;
    foreach (glob(rtrim($path, '/').'/*', GLOB_NOSORT) as $each) {
        $size += is_file($each) ? filesize($each) : getFolderSize($each);
    }
    return $size;
}

$pageTitle = 'System Wartung';
ob_start();
?>

<div class="maintenance-tool">
    <h1>System Wartung</h1>
    
    <?php if ($message): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($message); ?></div>
    <?php endif; ?>
    
    <?php if ($error): ?>
        <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <div class="maintenance-grid">
        <div class="maintenance-box">
            <h3>Log Management</h3>
            <form method="POST" action="">
                <input type="hidden" name="action" value="clear_logs">
                <p>Leert alle Log-Dateien des Systems.</p>
                <button type="submit" class="btn btn-warning">Logs leeren</button>
            </form>
        </div>
        
        <div class="maintenance-box">
            <h3>Daten Optimierung</h3>
            <form method="POST" action="">
                <input type="hidden" name="action" value="optimize_data">
                <p>Optimiert die Datendateien und entfernt ungültige Einträge.</p>
                <button type="submit" class="btn btn-warning">Daten optimieren</button>
            </form>
        </div>
        
        <div class="maintenance-box">
            <h3>System Information</h3>
            <form method="POST" action="">
                <input type="hidden" name="action" value="system_info">
                <p>Erstellt einen detaillierten System-Report.</p>
                <button type="submit" class="btn btn-primary">Report erstellen</button>
            </form>
        </div>
    </div>
</div>

<style>
.maintenance-tool {
    padding: 2rem;
}

.maintenance-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 2rem;
    margin-top: 2rem;
}

.maintenance-box {
    background: white;
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.maintenance-box h3 {
    margin-bottom: 1rem;
    color: #2c3e50;
}

.maintenance-box p {
    margin-bottom: 1rem;
    color: #666;
}

.btn-warning {
    background-color: #e67e22;
    color: white;
}

.btn-warning:hover {
    background-color: #d35400;
}
</style>

<?php
$content = ob_get_clean();
require 'templates/layout.php'; 