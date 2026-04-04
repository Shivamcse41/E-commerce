<?php
// fix_data.php
require_once 'config/db.php';

// Sync major sections
$sections = ['Men', 'Women', 'Kids', 'Beauty', 'Home & Living', 'Genz'];
foreach ($sections as $sec) {
    if (!$pdo->query("SELECT id FROM categories WHERE name='$sec'")->fetch()) {
        $pdo->query("INSERT INTO categories (name) VALUES ('$sec')");
    }
}

// Move existing products to 'Men' for demo purposes
$men_id = $pdo->query("SELECT id FROM categories WHERE name='Men'")->fetchColumn();
$clothing_id = $pdo->query("SELECT id FROM categories WHERE name='Clothing'")->fetchColumn();
$shoe_id = $pdo->query("SELECT id FROM categories WHERE name='shoe'")->fetchColumn();

if ($clothing_id) {
    $pdo->query("UPDATE products SET category_id=$men_id WHERE category_id=$clothing_id");
}
if ($shoe_id) {
    $pdo->query("UPDATE products SET category_id=$men_id WHERE category_id=$shoe_id");
}

echo "Data fixed for Myntra redesign demo.";
?>
