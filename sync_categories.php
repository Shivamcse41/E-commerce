<?php
// sync_categories.php
require_once 'config/db.php';
$categories = ['Men', 'Women', 'Kids', 'Beauty', 'Home & Living', 'Genz'];

foreach ($categories as $cat) {
    $stmt = $pdo->prepare("SELECT id FROM categories WHERE name = ?");
    $stmt->execute([$cat]);
    if (!$stmt->fetch()) {
        $pdo->prepare("INSERT INTO categories (name) VALUES (?)")->execute([$cat]);
    }
}
echo "Categories synced successfully.";
?>
