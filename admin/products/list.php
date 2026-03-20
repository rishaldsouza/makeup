<?php
session_start();
include '../../config/db.php';
include '../includes/header.php';

if (isset($_GET['delete_id'])) {
    $id = (int)$_GET['delete_id'];
    $res = mysqli_query($conn,"SELECT image FROM products WHERE id=$id");
    $row = mysqli_fetch_assoc($res);
    if ($row && $row['image'] && file_exists('../../assets/images/'.$row['image'])) unlink('../../assets/images/'.$row['image']);
    mysqli_query($conn,"DELETE FROM products WHERE id=$id");
    header("Location: list.php"); exit;
}

$res = mysqli_query($conn,"SELECT * FROM products ORDER BY id DESC");
?>
<?php include '../includes/sidebar.php'; ?>
<div class="main">
    <h1>Products</h1>
    <div class="page-card">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:20px">
            <h2>All Products</h2>
            <a href="add.php" class="btn btn-primary">+ Add Product</a>
        </div>
        <table>
            <thead><tr><th>Image</th><th>Name</th><th>Category</th><th>Price</th><th>Stock</th><th>Actions</th></tr></thead>
            <tbody>
            <?php while($p=mysqli_fetch_assoc($res)): ?>
            <tr>
                <td><img src="../../assets/images/<?=htmlspecialchars($p['image']??'')?>" width="60" height="60" style="border-radius:8px;object-fit:cover" onerror="this.src='../../assets/images/lipstick.jpg'"></td>
                <td style="font-weight:600"><?=htmlspecialchars($p['name'])?></td>
                <td><?=htmlspecialchars($p['category']??'General')?></td>
                <td>₹<?=number_format($p['price'],2)?></td>
                <td><?=$p['stock']??0?></td>
                <td style="display:flex;gap:8px;flex-wrap:wrap">
                    <a href="edit.php?id=<?=$p['id']?>" class="btn btn-edit">Edit</a>
                    <a href="list.php?delete_id=<?=$p['id']?>" class="btn btn-delete" onclick="return confirm('Delete this product?')">Delete</a>
                </td>
            </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
