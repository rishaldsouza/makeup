<?php
session_start();
include '../../config/db.php';
include '../includes/header.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) { header("Location: list.php"); exit; }
$p = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM products WHERE id=$id"));
if (!$p) { header("Location: list.php"); exit; }

if (isset($_POST['update'])) {
    $name     = mysqli_real_escape_string($conn, $_POST['name']);
    $price    = (float)$_POST['price'];
    $desc     = mysqli_real_escape_string($conn, $_POST['description']);
    $category = mysqli_real_escape_string($conn, $_POST['category']);
    $stock    = (int)$_POST['stock'];
    $img      = $p['image'];

    if (!empty($_FILES['image']['name'])) {
        $allowed = ['jpg','jpeg','png','webp'];
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext,$allowed)) {
            $newImg = time().'_'.$_FILES['image']['name'];
            if (move_uploaded_file($_FILES['image']['tmp_name'], '../../assets/images/'.$newImg)) {
                if ($img && file_exists('../../assets/images/'.$img)) unlink('../../assets/images/'.$img);
                $img = $newImg;
            }
        }
    }

    mysqli_query($conn,"UPDATE products SET name='$name',description='$desc',price=$price,image='$img',category='$category',stock=$stock WHERE id=$id");
    header("Location: list.php"); exit;
}
?>
<?php include '../includes/sidebar.php'; ?>
<div class="main">
    <h1>Edit Product</h1>
    <div class="page-card" style="max-width:600px">
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group"><label>Product Name *</label><input type="text" name="name" value="<?=htmlspecialchars($p['name'])?>" required></div>
            <div class="form-group"><label>Description</label><textarea name="description" rows="3" style="width:100%;padding:12px;border:1.5px solid #e0e0e0;border-radius:10px;font-family:inherit;resize:vertical"><?=htmlspecialchars($p['description']??'')?></textarea></div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px">
                <div class="form-group"><label>Price (₹) *</label><input type="number" name="price" value="<?=htmlspecialchars($p['price'])?>" min="0" step="0.01" required></div>
                <div class="form-group"><label>Stock</label><input type="number" name="stock" value="<?=(int)($p['stock']??0)?>" min="0"></div>
            </div>
            <div class="form-group"><label>Category</label>
                <select name="category">
                    <?php foreach(['Lips','Skincare','Fragrance','Eyes','Nails','General'] as $cat): ?>
                    <option <?=$cat===($p['category']??'')?'selected':''?>><?=$cat?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if($p['image']): ?>
            <div class="form-group"><label>Current Image</label><br><img src="../../assets/images/<?=htmlspecialchars($p['image'])?>" width="100" style="border-radius:8px;margin-top:8px" onerror="this.style.display='none'"></div>
            <?php endif; ?>
            <div class="form-group"><label>Replace Image</label><input type="file" name="image" accept="image/*"></div>
            <div style="display:flex;gap:12px">
                <button type="submit" name="update" class="btn btn-primary">Update Product</button>
                <a href="list.php" class="btn" style="background:#f0f0f5;color:var(--text)">Cancel</a>
            </div>
        </form>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
