<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? 'Marvin'); ?></title>
    <link rel="stylesheet" href="/marvin/assets/css/style.css">
    <?php if (strpos($_SERVER['PHP_SELF'], 'admin/') !== false): ?>
        <link rel="stylesheet" href="/marvin/assets/css/admin.css">
    <?php endif; ?>
    <!-- Add Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <a href="/marvin/index.php">Marvin</a>
            </div>
            
            <!-- Mobile menu button -->
            <button class="menu-toggle" aria-label="Toggle menu" aria-expanded="false">
                <i class="fas fa-bars"></i>
            </button>

            <ul class="nav-links">
                <li><a href="/marvin/index.php">Home</a></li>
                <li><a href="/marvin/gallery.php">Galerie</a></li>
                <li><a href="/marvin/calculator.php">Rechner</a></li>
                <li><a href="/marvin/contact.php">Kontakt</a></li>
                <?php if (isLoggedIn()): ?>
                    <li><a href="/marvin/create-post.php">Beitrag erstellen</a></li>
                    <?php if (isAdmin()): ?>
                        <li><a href="/marvin/admin/index.php">Admin</a></li>
                    <?php endif; ?>
                    <li><a href="/marvin/logout.php">Logout</a></li>
                <?php else: ?>
                    <li><a href="/marvin/login.php">Login</a></li>
                    <li><a href="/marvin/register.php">Registrieren</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </header>

    <main class="container fade-in">
        <?php
        if (isset($_SESSION['success'])) {
            echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['success']) . '</div>';
            unset($_SESSION['success']);
        }
        if (isset($_SESSION['error'])) {
            echo '<div class="alert alert-error">' . htmlspecialchars($_SESSION['error']) . '</div>';
            unset($_SESSION['error']);
        }
        
        echo ob_get_clean();
        ?>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?php echo date('Y'); ?> Marvin. Alle Rechte vorbehalten.</p>
        </div>
    </footer>

    <!-- Add our JavaScript file -->
    <script src="/marvin/assets/js/main.js"></script>
</body>
</html> 