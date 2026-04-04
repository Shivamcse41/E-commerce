<?php
$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll();

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $action = $_POST['action'];
    if($action == 'delete'){
        // Protect against deleting yourself
        $id = $_POST['id'];
        if($id != $_SESSION['user_id']) {
            $pdo->prepare("DELETE FROM users WHERE id=?")->execute([$id]);
            $_SESSION['success'] = "User deleted.";
        } else {
            $_SESSION['error'] = "You cannot delete yourself.";
        }
    }
    header("Location: index.php?page=admin_users");
    exit;
}
?>

<div class="row">
    <div class="col-12 mb-4">
        <div class="rounded-card p-4">
            <h5 class="fw-bold mb-3">Manage Users</h5>
            <div class="table-responsive">
                <table class="table table-hover align-middle table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Profile</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($users as $u): ?>
                        <tr>
                            <td><?php echo $u['id']; ?></td>
                            <td><img src="uploads/<?php echo htmlspecialchars($u['profile_pic']); ?>" width="40" height="40" class="rounded-circle" style="object-fit:cover" onerror="this.src='https://via.placeholder.com/40'"></td>
                            <td class="fw-bold"><?php echo htmlspecialchars($u['full_name']); ?></td>
                            <td><?php echo htmlspecialchars($u['email']); ?></td>
                            <td>
                                <?php 
                                    $badge = 'bg-secondary';
                                    if($u['role'] == 'admin') $badge = 'bg-danger';
                                    if($u['role'] == 'seller') $badge = 'bg-info';
                                    if($u['role'] == 'customer') $badge = 'bg-primary';
                                ?>
                                <span class="badge <?php echo $badge; ?> rounded-pill px-3"><?php echo strtoupper($u['role']); ?></span>
                            </td>
                            <td>
                                <?php if($u['id'] != $_SESSION['user_id']): ?>
                                <form method="POST" onsubmit="return confirm('Delete this user? All their products/orders will be deleted too.');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="id" value="<?php echo $u['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle"><i class="fas fa-trash"></i></button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
