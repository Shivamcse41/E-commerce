<?php
$user_id = $_SESSION['user_id'];

// Mark all as read when visited
$pdo->prepare("UPDATE notifications SET is_read=TRUE WHERE user_id=?")->execute([$user_id]);

$stmt = $pdo->prepare("SELECT * FROM notifications WHERE user_id=? ORDER BY created_at DESC");
$stmt->execute([$user_id]);
$notifications = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">Notifications</h3>
</div>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="rounded-card p-4">
            <?php if(count($notifications) > 0): ?>
                <div class="list-group list-group-flush">
                    <?php foreach($notifications as $notif): ?>
                    <div class="list-group-item list-group-item-action d-flex align-items-center py-3 border-bottom">
                        <div class="bg-light p-3 rounded-circle text-primary me-3">
                            <i class="fas fa-bell"></i>
                        </div>
                        <div>
                            <p class="mb-1 fw-medium"><?php echo htmlspecialchars($notif['message']); ?></p>
                            <small class="text-muted"><?php echo date('M d, Y h:i A', strtotime($notif['created_at'])); ?></small>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="far fa-bell-slash fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted">No notifications right now.</h5>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
