<?php
$categories = $pdo->query("SELECT * FROM categories ORDER BY created_at DESC")->fetchAll();

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $action = $_POST['action'];
    if($action == 'add'){
        $name = $_POST['name'];
        $pdo->prepare("INSERT INTO categories (name) VALUES (?)")->execute([$name]);
        $_SESSION['success'] = "Category added.";
    } elseif($action == 'delete'){
        $id = $_POST['id'];
        $pdo->prepare("DELETE FROM categories WHERE id=?")->execute([$id]);
        $_SESSION['success'] = "Category deleted.";
    }
    header("Location: index.php?page=admin_categories");
    exit;
}
?>

<div class="row">
    <div class="col-md-4 mb-4">
        <div class="rounded-card p-4">
            <h5 class="fw-bold mb-3">Add Category</h5>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="mb-3">
                    <label class="form-label">Category Name</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary-gradient rounded-pill w-100 py-2">Add Category</button>
            </form>
        </div>
    </div>
    
    <div class="col-md-8 mb-4">
        <div class="rounded-card p-4">
            <h5 class="fw-bold mb-3">Manage Categories</h5>
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($categories as $cat): ?>
                    <tr>
                        <td><?php echo $cat['id']; ?></td>
                        <td class="fw-bold"><?php echo htmlspecialchars($cat['name']); ?></td>
                        <td><?php echo date('M d, Y', strtotime($cat['created_at'])); ?></td>
                        <td>
                            <form method="POST" onsubmit="return confirm('Delete this category? All related products will also be deleted!');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $cat['id']; ?>">
                                <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
