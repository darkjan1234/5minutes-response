<?php
$stmt = $pdo->prepare("SELECT * FROM reports WHERE user_id = ? ORDER BY submitted_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$my_reports = $stmt->fetchAll();
?>

<h2>My Reports</h2>

<?php if (empty($my_reports)): ?>
    <div class="alert alert-info">
        You haven't submitted any reports yet. <a href="?page=report">Submit your first report</a>
    </div>
<?php else: ?>
    <div class="row">
        <?php foreach ($my_reports as $report): ?>
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
                        <p><?= htmlspecialchars($report['description']) ?></p>
                        
                        <?php if ($report['photo_path']): ?>
                            <img src="<?= $report['photo_path'] ?>" class="img-fluid mb-2" style="max-height: 200px;">
                        <?php endif; ?>
                        
                        <p><small>
                            <strong>Submitted:</strong> <?= $report['submitted_at'] ?><br>
                            <?php if ($report['acknowledged_at']): ?>
                                <strong>Acknowledged:</strong> <?= $report['acknowledged_at'] ?><br>
                            <?php endif; ?>
                            <?php if ($report['escalated']): ?>
                                <span class="text-danger"><strong>Escalated to supervisor</strong></span>
                            <?php endif; ?>
                        </small></p>
                        
                        <?php if ($report['status'] == 'pending' && !$report['escalated']): ?>
                            <div class="alert alert-warning">
                                <strong>Timer:</strong> 
                                <span id="timer-<?= $report['id'] ?>" data-expires="<?= $report['timer_expires_at'] ?>">
                                    <?= timeRemaining($report['timer_expires_at']) ?>
                                </span>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>