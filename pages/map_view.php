<?php
if ($_SESSION['role'] == 'citizen') {
    header('Location: ?page=dashboard');
    exit;
}

// Get all reports with location data
$stmt = $pdo->prepare("
    SELECT r.*, u.username 
    FROM reports r 
    JOIN users u ON r.user_id = u.id 
    WHERE r.latitude IS NOT NULL AND r.longitude IS NOT NULL
    ORDER BY r.submitted_at DESC
");
$stmt->execute();
$reports = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Reports Map View</h2>
    <div>
        <button class="btn btn-outline-primary btn-sm" onclick="filterReports('all')">All</button>
        <button class="btn btn-outline-danger btn-sm" onclick="filterReports('pending')">Pending</button>
        <button class="btn btn-outline-warning btn-sm" onclick="filterReports('acknowledged')">Acknowledged</button>
        <button class="btn btn-outline-success btn-sm" onclick="filterReports('resolved')">Resolved</button>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body p-0">
                <div id="reports-map" style="height: 600px;"></div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5>Report Details</h5>
            </div>
            <div class="card-body" id="report-details">
                <p class="text-muted">Click on a map marker to view report details</p>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5>Legend</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-2">
                    <div style="width: 20px; height: 20px; background: #dc3545; border-radius: 50%; margin-right: 10px;"></div>
                    <span>Pending (Unacknowledged)</span>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <div style="width: 20px; height: 20px; background: #ffc107; border-radius: 50%; margin-right: 10px;"></div>
                    <span>Acknowledged</span>
                </div>
                <div class="d-flex align-items-center mb-2">
                    <div style="width: 20px; height: 20px; background: #007bff; border-radius: 50%; margin-right: 10px;"></div>
                    <span>In Progress</span>
                </div>
                <div class="d-flex align-items-center">
                    <div style="width: 20px; height: 20px; background: #28a745; border-radius: 50%; margin-right: 10px;"></div>
                    <span>Resolved</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Pass PHP data to JavaScript
const reportsData = <?= json_encode($reports) ?>;
</script>