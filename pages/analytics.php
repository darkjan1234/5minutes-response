<?php
if ($_SESSION['role'] != 'supervisor') {
    header('Location: ?page=dashboard');
    exit;
}

// Get statistics
$total_reports = $pdo->query("SELECT COUNT(*) FROM reports")->fetchColumn();
$pending_reports = $pdo->query("SELECT COUNT(*) FROM reports WHERE status = 'pending'")->fetchColumn();
$resolved_reports = $pdo->query("SELECT COUNT(*) FROM reports WHERE status = 'resolved'")->fetchColumn();
$escalated_reports = $pdo->query("SELECT COUNT(*) FROM reports WHERE escalated = TRUE")->fetchColumn();

// Get reports by issue type
$issue_stats = $pdo->query("
    SELECT issue_type, COUNT(*) as count 
    FROM reports 
    GROUP BY issue_type 
    ORDER BY count DESC
")->fetchAll();

// Get average response time
$avg_response = $pdo->query("
    SELECT AVG(TIMESTAMPDIFF(MINUTE, submitted_at, acknowledged_at)) as avg_minutes
    FROM reports 
    WHERE acknowledged_at IS NOT NULL
")->fetchColumn();
?>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5>Total Reports</h5>
                <h2><?= $total_reports ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <h5>Pending</h5>
                <h2><?= $pending_reports ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5>Resolved</h5>
                <h2><?= $resolved_reports ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h5>Escalated</h5>
                <h2><?= $escalated_reports ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Reports by Issue Type</h5>
            </div>
            <div class="card-body">
                <?php foreach ($issue_stats as $stat): ?>
                    <div class="d-flex justify-content-between">
                        <span><?= htmlspecialchars($stat['issue_type']) ?></span>
                        <span class="badge bg-primary"><?= $stat['count'] ?></span>
                    </div>
                    <hr>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Performance Metrics</h5>
            </div>
            <div class="card-body">
                <p><strong>Average Response Time:</strong> 
                   <?= $avg_response ? round($avg_response, 1) . ' minutes' : 'No data' ?>
                </p>
                <p><strong>Escalation Rate:</strong> 
                   <?= $total_reports > 0 ? round(($escalated_reports / $total_reports) * 100, 1) : 0 ?>%
                </p>
            </div>
        </div>
    </div>
</div>