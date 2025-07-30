<?php
session_start();
require_once 'config/database.php';
require_once 'includes/functions.php';

$page = $_GET['page'] ?? 'home';

if (!isset($_SESSION['user_id']) && !in_array($page, ['home', 'login', 'register'])) {
    header('Location: ?page=login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barangay Reports System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <?php include 'includes/navbar.php'; ?>
    
    <div class="container mt-4">
        <?php
        switch($page) {
            case 'login':
                include 'pages/login.php';
                break;
            case 'register':
                include 'pages/register.php';
                break;
            case 'dashboard':
                include 'pages/dashboard.php';
                break;
            case 'report':
                include 'pages/report_form.php';
                break;
            case 'my_reports':
                include 'pages/my_reports.php';
                break;
            case 'map_view':
                include 'pages/map_view.php';
                break;
            case 'analytics':
                include 'pages/analytics.php';
                break;
            case 'logout':
                include 'pages/logout.php';
                break;
            default:
                include 'pages/home.php';
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="assets/script.js"></script>
    <script src="assets/map.js"></script>
</body>
</html>




