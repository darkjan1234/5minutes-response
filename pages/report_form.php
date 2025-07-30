<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $issue_type = $_POST['issue_type'];
    $description = $_POST['description'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    
    $photo_path = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $photo_path = uploadPhoto($_FILES['photo']);
    }
    
    $timer_expires_at = date('Y-m-d H:i:s', strtotime('+5 minutes'));
    
    $stmt = $pdo->prepare("
        INSERT INTO reports (user_id, issue_type, description, photo_path, latitude, longitude, timer_expires_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    
    if ($stmt->execute([$_SESSION['user_id'], $issue_type, $description, $photo_path, $latitude, $longitude, $timer_expires_at])) {
        $success = "Report submitted successfully!";
    } else {
        $error = "Failed to submit report";
    }
}
?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4>Submit New Report</h4>
            </div>
            <div class="card-body">
                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                
                <form method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Issue Type</label>
                        <select name="issue_type" class="form-control" required>
                            <option value="">Select Issue Type</option>
                            <option value="Stray Animals">Stray Animals</option>
                            <option value="Garbage">Garbage</option>
                            <option value="Noise Disturbance">Noise Disturbance</option>
                            <option value="Street Lighting">Street Lighting</option>
                            <option value="Road Damage">Road Damage</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="4" required></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Photo (Optional)</label>
                        <input type="file" name="photo" class="form-control" accept="image/*">
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Latitude</label>
                                <input type="number" name="latitude" id="latitude" class="form-control" step="any" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Longitude</label>
                                <input type="number" name="longitude" id="longitude" class="form-control" step="any" required>
                            </div>
                        </div>
                    </div>
                    
                    <button type="button" class="btn btn-secondary" onclick="getLocation()">Get My Location</button>
                    <button type="submit" class="btn btn-primary">Submit Report</button>
                </form>
            </div>
        </div>
    </div>
</div>