<?php
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

$stmt = $pdo->prepare("SELECT p.*, c.name as category_name, u.full_name as seller_name FROM products p JOIN categories c ON p.category_id = c.id JOIN users u ON p.seller_id = u.id ORDER BY p.created_at DESC");
$stmt->execute();
$products = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">All Products</h3>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="rounded-card p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle table-striped">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Seller</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($products as $prod): ?>
                        <tr>
                            <td><img src="uploads/<?php echo htmlspecialchars($prod['product_image']); ?>" width="50" height="50" class="rounded" style="object-fit:cover" onerror="this.src='https://via.placeholder.com/50'"></td>
                            <td class="fw-bold"><?php echo htmlspecialchars($prod['name']); ?></td>
                            <td><span class="badge bg-secondary"><?php echo htmlspecialchars($prod['seller_name']); ?></span></td>
                            <td><?php echo htmlspecialchars($prod['category_name']); ?></td>
                            <td>₹<?php echo number_format($prod['price'], 2); ?></td>
                            <td>
                                <button class="btn btn-sm btn-outline-info rounded-circle me-1" data-bs-toggle="modal" data-bs-target="#editProductModal<?php echo $prod['id']; ?>"><i class="fas fa-edit"></i></button>
                                <form method="POST" action="actions/product_action.php" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this product?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="product_id" value="<?php echo $prod['id']; ?>">
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-circle"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>

                        <!-- Edit Product Modal -->
                        <div class="modal fade" id="editProductModal<?php echo $prod['id']; ?>" tabindex="-1" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content" style="border-radius:15px;">
                                    <form method="POST" action="actions/product_action.php" enctype="multipart/form-data">
                                        <div class="modal-header">
                                            <h5 class="modal-title fw-bold">Edit Product</h5>
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <input type="hidden" name="action" value="edit">
                                            <input type="hidden" name="product_id" value="<?php echo $prod['id']; ?>">
                                            
                                            <div class="mb-3">
                                                <label class="form-label">Product Name</label>
                                                <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($prod['name']); ?>" required>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Category</label>
                                                <select name="category_id" class="form-select" required>
                                                    <?php foreach($categories as $cat): ?>
                                                        <option value="<?php echo $cat['id']; ?>" <?php echo $cat['id']==$prod['category_id']?'selected':''; ?>><?php echo htmlspecialchars($cat['name']); ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Price (₹)</label>
                                                    <input type="number" step="0.01" name="price" class="form-control" value="<?php echo htmlspecialchars($prod['price']); ?>" required>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Stock</label>
                                                    <input type="number" name="stock" class="form-control" value="<?php echo htmlspecialchars($prod['stock']); ?>" required>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label">Description</label>
                                                <textarea name="description" class="form-control" rows="3"><?php echo htmlspecialchars($prod['description']); ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label d-flex justify-content-between">
                                                    <span>Product Image</span>
                                                    <a href="https://www.google.com/search?q=<?php echo urlencode($prod['name']); ?>&tbm=isch" target="_blank" class="text-decoration-none small text-primary"><i class="fas fa-search me-1"></i> Search on Google</a>
                                                </label>
                                                <div class="input-group mb-2">
                                                    <span class="input-group-text bg-light"><i class="fas fa-link"></i></span>
                                                    <input type="url" name="image_url" class="form-control" placeholder="Paste Image URL from internet..." oninput="previewImage(this, 'previewAdminEdit<?php echo $prod['id']; ?>')">
                                                </div>
                                                <div class="text-center mb-2">OR</div>
                                                <input type="file" name="product_image" class="form-control" accept="image/*" onchange="previewFile(this, 'previewAdminEdit<?php echo $prod['id']; ?>')">
                                            </div>
                                            <div class="text-center mt-3">
                                                <img id="previewAdminEdit<?php echo $prod['id']; ?>" src="uploads/<?php echo htmlspecialchars($prod['product_image']); ?>" class="img-thumbnail" style="max-height: 150px; object-fit: contain;" onerror="this.src='https://via.placeholder.com/150'">
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                                            <button type="submit" class="btn btn-primary-gradient rounded-pill py-2 px-4">Save Changes</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if(count($products) == 0): ?>
                <div class="text-center py-4 text-muted">No products found.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
<script>
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if(input.value) {
        preview.src = input.value;
    }
}

function previewFile(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
        }
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
