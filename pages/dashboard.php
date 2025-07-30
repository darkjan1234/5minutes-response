<?php
checkTimer($pdo);

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $report_id = $_POST['report_id'];
    $action = $_POST['action'];
    
    switch ($action) {
        case 'acknowledge':
            $stmt = $pdo->prepare("
                UPDATE reports 
                SET status = 'acknowledged', acknowledged_by = ?, acknowledged_at = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([$_SESSION['user_id'], $report_id]);
            break;
            
        case 'in_progress':
            $stmt = $pdo->prepare("UPDATE reports SET status = 'in_progress' WHERE id = ?");
            $stmt->execute([$report_id]);
            break;
            
        case 'resolved':
            $stmt = $pdo->prepare("UPDATE reports SET status = 'resolved' WHERE id = ?");
            $stmt->execute([$report_id]);
            break;
    }
    
    header('Location: ?page=dashboard');
    exit;
}

if ($_SESSION['role'] == 'citizen') {
    $stmt = $pdo->prepare("SELECT * FROM reports WHERE user_id = ? ORDER BY submitted_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
} else {
    $stmt = $pdo->prepare("
        SELECT r.*, u.username 
        FROM reports r 
        JOIN users u ON r.user_id = u.id 
        ORDER BY r.submitted_at DESC
    ");
    $stmt->execute();
}
$reports = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Dashboard</h2>
    <?php if ($_SESSION['role'] == 'citizen'): ?>
        <a href="?page=report" class="btn btn-primary">Submit New Report</a>
    <?php endif; ?>
</div>

<div class="row">
    <?php foreach ($reports as $report): ?>
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header d-flex justify-content-between">
                    <span><strong><?= htmlspecialchars($report['issue_type']) ?></strong></span>
                    <span class="badge bg-<?= 
                        $report['status'] == 'pending' ? 'warning' : 
                        ($report['status'] == 'acknowledged' ? 'info' : 
                        ($report['status'] == 'in_progress' ? 'primary' : 'success')) 
                    ?>">
                        <?= ucfirst($report['status']) ?>
                    </span>
                </div>
                <div class="card-body">
                    <?php if (isset($report['username'])): ?>
                        <p><strong>Reporter:</strong> <?= htmlspecialchars($report['username']) ?></p>
                    <?php endif; ?>
                    
                    <p><?= htmlspecialchars($report['description']) ?></p>
                    
                    <?php if ($report['photo_path']): ?>
                        <img src="<?= $report['photo_path'] ?>" class="img-fluid mb-2" style="max-height: 200px;">
                    <?php endif; ?>
                    
                    <p><small>
                        <strong>Location:</strong> <?= $report['latitude'] ?>, <?= $report['longitude'] ?><br>
                        <strong>Submitted:</strong> <?= $report['submitted_at'] ?>
                    </small></p>
                    
                    <?php if ($report['status'] == 'pending' && !$report['escalated']): ?>
                        <div class="alert alert-warning">
                            <strong>Timer:</strong> 
                            <span id="timer-<?= $report['id'] ?>" data-expires="<?= $report['timer_expires_at'] ?>">
                                <?= timeRemaining($report['timer_expires_at']) ?>
                            </span>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($report['escalated']): ?>
                        <div class="alert alert-danger">
                            <strong>ESCALATED</strong> - Timer expired
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($_SESSION['role'] != 'citizen' && $report['status'] != 'resolved'): ?>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="report_id" value="<?= $report['id'] ?>">
                            <?php if ($report['status'] == 'pending'): ?>
                                <button type="submit" name="action" value="acknowledge" class="btn btn-sm btn-info">Acknowledge</button>
                            <?php endif; ?>
                            <?php if ($report['status'] == 'acknowledged'): ?>
                                <button type="submit" name="action" value="in_progress" class="btn btn-sm btn-primary">Start Progress</button>
                            <?php endif; ?>
                            <?php if ($report['status'] == 'in_progress'): ?>
                                <button type="submit" name="action" value="resolved" class="btn btn-sm btn-success">Mark Resolved</button>
                            <?php endif; ?>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>