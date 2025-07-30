<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="index.php">Barangay Reports</a>
        
        <div class="navbar-nav ms-auto">
            <?php if (isset($_SESSION['user_id'])): ?>
                <a class="nav-link" href="?page=dashboard">Dashboard</a>
                <?php if ($_SESSION['role'] == 'citizen'): ?>
                    <a class="nav-link" href="?page=report">Submit Report</a>
                    <a class="nav-link" href="?page=my_reports">My Reports</a>
                <?php endif; ?>
                <?php if ($_SESSION['role'] == 'supervisor'): ?>
                    <a class="nav-link" href="?page=analytics">Analytics</a>
                <?php endif; ?>
                <a class="nav-link" href="?page=logout">Logout (<?= $_SESSION['username'] ?>)</a>
            <?php else: ?>
                <a class="nav-link" href="?page=login">Login</a>
                <a class="nav-link" href="?page=register">Register</a>
            <?php endif; ?>
        </div>
    </div>
</nav>