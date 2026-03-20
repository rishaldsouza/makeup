<?php
session_start();
include '../config/db.php';
if (!isset($_SESSION['user'])) { header("Location: ../login.php"); exit; }

$user_id    = (int)$_SESSION['user'];
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($product_id <= 0) { header("Location: ../index.php"); exit; }

// Check product exists
$pchk = $conn->prepare("SELECT name FROM products WHERE id=?");
$pchk->bind_param("i",$product_id);
$pchk->execute();
$prod = $pchk->get_result()->fetch_assoc();
if (!$prod) { header("Location: ../index.php"); exit; }

// Check duplicate
$stmt = $conn->prepare("SELECT id FROM wishlist WHERE user_id=? AND product_id=?");
$stmt->bind_param("ii",$user_id,$product_id);
$stmt->execute();
$exists = $stmt->get_result()->fetch_assoc();

if (!$exists) {
    $ins = $conn->prepare("INSERT INTO wishlist (user_id, product_id) VALUES (?,?)");
    $ins->bind_param("ii",$user_id,$product_id);
    $ins->execute();
}
// Redirect back with notice
header("Location: ../product.php?id=$product_id&wishlisted=1");
exit;
