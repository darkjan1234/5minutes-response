<?php
function checkTimer($pdo) {
    $stmt = $pdo->prepare("
        SELECT id, timer_expires_at, user_id 
        FROM reports 
        WHERE status = 'pending' 
        AND timer_expires_at < NOW() 
        AND escalated = FALSE
    ");
    $stmt->execute();
    $expired_reports = $stmt->fetchAll();
    
    foreach ($expired_reports as $report) {
        // Mark as escalated
        $update_stmt = $pdo->prepare("UPDATE reports SET escalated = TRUE WHERE id = ?");
        $update_stmt->execute([$report['id']]);
        
        // Insert escalation record
        $escalate_stmt = $pdo->prepare("
            INSERT INTO escalations (report_id, escalated_to, reason) 
            VALUES (?, 1, 'Timer expired - no response within 5 minutes')
        ");
        $escalate_stmt->execute([$report['id']]);
    }
}

function timeRemaining($expires_at) {
    $now = new DateTime();
    $expires = new DateTime($expires_at);
    $diff = $expires->getTimestamp() - $now->getTimestamp();
    
    if ($diff <= 0) return "EXPIRED";
    
    $minutes = floor($diff / 60);
    $seconds = $diff % 60;
    return sprintf("%02d:%02d", $minutes, $seconds);
}

function uploadPhoto($file) {
    $upload_dir = 'uploads/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = uniqid() . '.' . $file_extension;
    $upload_path = $upload_dir . $new_filename;
    
    if (move_uploaded_file($file['tmp_name'], $upload_path)) {
        return $upload_path;
    }
    return false;
}
?>