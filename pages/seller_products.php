<?php
$seller_id = $_SESSION['user_id'];
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

$stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.seller_id = ? ORDER BY p.created_at DESC");
$stmt->execute([$seller_id]);
$products = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h3 class="fw-bold">My Products</h3>
    <button class="btn btn-primary-gradient rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addProductModal"><i class="fas fa-plus me-2"></i> Add Product</button>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="rounded-card p-4">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Added On</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($products as $prod): ?>
                        <tr>
                            <td><img src="uploads/<?php echo htmlspecialchars($prod['product_image']); ?>" width="50" height="50" class="rounded" style="object-fit:cover" onerror="this.src='https://via.placeholder.com/50'"></td>
                            <td class="fw-bold"><?php echo htmlspecialchars($prod['name']); ?></td>
                            <td><?php echo htmlspecialchars($prod['category_name']); ?></td>
                            <td>₹<?php echo number_format($prod['price'], 2); ?></td>
                            <td>
                                <?php if($prod['stock'] > 10): ?>
                                    <span class="badge bg-success"><?php echo $prod['stock']; ?></span>
                                <?php elseif($prod['stock'] > 0): ?>
                                    <span class="badge bg-warning text-dark"><?php echo $prod['stock']; ?></span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Out of Stock</span>
                                <?php endif; ?>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($prod['created_at'])); ?></td>
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
                                                    <input type="url" name="image_url" class="form-control" placeholder="Paste Image URL from internet..." oninput="previewImage(this, 'previewEdit<?php echo $prod['id']; ?>')">
                                                </div>
                                                <div class="text-center mb-2">OR</div>
                                                <input type="file" name="product_image" class="form-control" accept="image/*" onchange="previewFile(this, 'previewEdit<?php echo $prod['id']; ?>')">
                                            </div>
                                            <div class="text-center mt-3">
                                                <img id="previewEdit<?php echo $prod['id']; ?>" src="uploads/<?php echo htmlspecialchars($prod['product_image']); ?>" class="img-thumbnail" style="max-height: 150px; object-fit: contain;" onerror="this.src='https://via.placeholder.com/150'">
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
                <div class="text-center py-4 text-muted">You haven't added any products yet.</div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:15px;">
            <form method="POST" action="actions/product_action.php" enctype="multipart/form-data">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add New Product</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    
                    <div class="mb-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Select Category</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Price (₹)</label>
                            <input type="number" step="0.01" name="price" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stock</label>
                            <input type="number" name="stock" class="form-control" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label d-flex justify-content-between">
                            <span>Product Image</span>
                            <button type="button" class="btn btn-link p-0 text-decoration-none small text-primary" onclick="searchGoogleForProduct(this)"><i class="fas fa-search me-1"></i> Search on Google</button>
                        </label>
                        <div class="input-group mb-2">
                            <span class="input-group-text bg-light"><i class="fas fa-link"></i></span>
                            <input type="url" name="image_url" class="form-control" placeholder="Paste Image URL from internet..." oninput="previewImage(this, 'previewAdd')">
                        </div>
                        <div class="text-center mb-2">OR</div>
                        <input type="file" name="product_image" class="form-control" accept="image/*" onchange="previewFile(this, 'previewAdd')">
                    </div>
                    <div class="text-center mt-3">
                        <img id="previewAdd" src="https://via.placeholder.com/150" class="img-thumbnail" style="max-height: 150px; object-fit: contain;">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary-gradient rounded-pill py-2 px-4">Add Product</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
function searchGoogleForProduct(btn) {
    const nameInput = btn.closest('.modal-body').querySelector('input[name="name"]');
    if(nameInput.value) {
        window.open('https://www.google.com/search?q=' + encodeURIComponent(nameInput.value) + '&tbm=isch', '_blank');
    } else {
        alert('Please enter a product name first!');
    }
}

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
