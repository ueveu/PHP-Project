<!DOCTYPE html>
<html lang="de">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - <?php echo SITE_NAME; ?> - <?php echo $pageTitle ?? 'Dashboard'; ?></title>
    <style>
        /* Admin Panel Base Styles */
        body {
            background-color: #f5f6fa;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        header {
            background-color: #2c3e50;
            padding: 1rem 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        .logo a {
            color: white;
            font-size: 1.5rem;
            font-weight: bold;
            text-decoration: none;
        }

        nav {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-links {
            display: flex;
            gap: 1.5rem;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .nav-links a {
            color: #ecf0f1;
            text-decoration: none;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 4px;
            transition: background-color 0.3s;
        }

        .nav-links a:hover {
            background-color: rgba(255,255,255,0.1);
        }

        main {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 1rem;
        }

        .admin-dashboard {
            padding: 2rem;
        }

        .admin-dashboard h1 {
            color: #2c3e50;
            margin: 0 0 2rem 0;
            font-size: 2rem;
            padding-bottom: 0.5rem;
            border-bottom: 3px solid #3498db;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-box {
            background: white;
            padding: 1.5rem;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .stat-box:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }

        .stat-box h3 {
            color: #2c3e50;
            margin-bottom: 1.5rem;
            font-size: 1.25rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #e1e8ed;
        }

        .stat-box ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .stat-box ul li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 0;
            border-bottom: 1px solid #e1e8ed;
            color: #34495e;
            font-size: 1rem;
        }

        .stat-box ul li:last-child {
            border-bottom: none;
        }

        .table-container {
            overflow-x: auto;
            margin-top: 1rem;
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
            background: white;
            font-size: 0.9rem;
        }

        .admin-table th,
        .admin-table td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid #e1e8ed;
        }

        .admin-table th {
            background: #f8f9fa;
            color: #2c3e50;
            font-weight: 600;
            white-space: nowrap;
        }

        .admin-table td {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .admin-table tr:hover {
            background-color: #f8f9fa;
        }

        .btn {
            display: inline-block;
            padding: 0.75rem 1.5rem;
            border-radius: 4px;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
        }

        .btn-primary {
            background: #3498db;
            color: white;
        }

        .btn-primary:hover {
            background: #2980b9;
        }

        .admin-actions {
            margin-top: 1.5rem;
            padding-top: 1rem;
            border-top: 1px solid #e1e8ed;
            text-align: center;
        }

        footer {
            background: #2c3e50;
            color: white;
            text-align: center;
            padding: 1.5rem;
            margin-top: 3rem;
        }

        .alert {
            padding: 1rem 1.5rem;
            border-radius: 4px;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
        }

        .alert-success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 768px) {
            nav {
                flex-direction: column;
                gap: 1rem;
            }

            .nav-links {
                flex-direction: column;
                width: 100%;
                text-align: center;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .admin-dashboard {
                padding: 1rem;
            }
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="logo">
                <a href="../index.php"><?php echo SITE_NAME; ?></a>
            </div>
            <ul class="nav-links">
                <li><a href="../index.php">Zurück zur Website</a></li>
                <li><a href="index.php">Dashboard</a></li>
                <li><a href="maintenance.php">System Wartung</a></li>
                <li><a href="../logout.php">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a></li>
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