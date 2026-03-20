<?php
session_start();
include '../../config/db.php';
include '../includes/header.php';

if (isset($_POST['add'])) {
    $name     = mysqli_real_escape_string($conn, $_POST['name']);
    $price    = (float)$_POST['price'];
    $desc     = mysqli_real_escape_string($conn, $_POST['description']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $stock    = (int)$_POST['stock'];
    $img      = '';

    if (!empty($_FILES['image']['name'])) {
        $allowed = ['jpg','jpeg','png','webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed)) { $error = "Only JPG, PNG, WebP allowed."; }
        else {
            $img = time().'_'.$_FILES['image']['name'];
            if (!move_uploaded_file($_FILES['image']['tmp_name'], '../../assets/images/'.$img)) { $error = "Image upload failed."; $img=''; }
        }
    }

    if (!isset($error)) {
        $stmt = $conn->prepare("INSERT INTO products(name,description,price,image,category,stock) VALUES(?,?,?,?,?,?)");
        $stmt->bind_param("ssdssi",$name,$desc,$price,$img,$category,$stock);
        if ($stmt->execute()) { header("Location: list.php"); exit; }
        $error = "Failed to add product.";
    }
}
?>
<?php include '../includes/sidebar.php'; ?>
<div class="main">
    <h1>Add New Product</h1>
    <div class="page-card" style="max-width:600px">
        <?php if(isset($error)): ?><div style="background:#ffebee;color:#c62828;padding:12px;border-radius:8px;margin-bottom:16px"><?=htmlspecialchars($error)?></div><?php endif; ?>
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group"><label>Product Name *</label><input type="text" name="name" required></div>
            <div class="form-group"><label>Description</label><textarea name="description" rows="3" style="width:100%;padding:12px;border:1.5px solid #e0e0e0;border-radius:10px;font-family:inherit;resize:vertical"></textarea></div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div class="form-group"><label>Price (₹) *</label><input type="number" name="price" min="0" step="0.01" required></div>
                <div class="form-group"><label>Stock</label><input type="number" name="stock" min="0" value="0"></div>
            </div>
            <div class="form-group"><label>Category</label>
                <select name="category">
                    <option>Lips</option><option>Skincare</option><option>Fragrance</option>
                    <option>Eyes</option><option>Nails</option><option>General</option>
                </select>
            </div>
            <div class="form-group"><label>Product Image</label><input type="file" name="image" accept="image/*"></div>
            <div style="display:flex;gap:12px">
                <button type="submit" name="add" class="btn btn-primary">Add Product</button>
                <a href="list.php" class="btn" style="background:#f0f0f5;color:var(--text)">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
