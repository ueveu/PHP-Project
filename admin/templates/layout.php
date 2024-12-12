<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo SITE_NAME; ?> - Admin Bereich</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <a href="index.php"><?php echo SITE_NAME; ?> - Admin</a>
            </div>
            <ul class="nav-links">
                <li><a href="../index.php">Zurück zur Website</a></li>
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="maintenance.php">System Wartung</a></li>
                <li><a href="../logout.php">Logout (<?php echo isset($_SESSION['alias']) ? htmlspecialchars($_SESSION['alias']) : ''; ?>)</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-error">
                <?php 
                echo htmlspecialchars($_SESSION['error']);
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php 
                echo htmlspecialchars($_SESSION['success']);
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <?php echo $content; ?>
    </main>

    <footer>
        <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?> - Admin Bereich</p>
    </footer>
</body>
</html> 