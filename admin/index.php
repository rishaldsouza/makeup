<?php
session_start();
include '../config/db.php';
include 'includes/header.php';

$p = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM products"))[0] ?? 0;
$u = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM users"))[0] ?? 0;
$o = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM orders"))[0] ?? 0;
$rev = mysqli_fetch_row(mysqli_query($conn,"SELECT COALESCE(SUM(total),0) FROM orders"))[0] ?? 0;
?>

<?php include 'includes/sidebar.php'; ?>

<div class="main">
    <h1>Dashboard</h1>
    <div class="cards">
        <div class="stat-card"><div class="icon">🛍</div><h4>Total Products</h4><div class="number"><?=$p?></div></div>
        <div class="stat-card"><div class="icon">👥</div><h4>Total Users</h4><div class="number"><?=$u?></div></div>
        <div class="stat-card"><div class="icon">📦</div><h4>Total Orders</h4><div class="number"><?=$o?></div></div>
    </div>
    <div class="page-card">
        <h2>Total Revenue: ₹<?=number_format($rev,2)?></h2>
        <p style="color:var(--muted);font-size:14px;margin-top:8px">From all completed orders</p>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
