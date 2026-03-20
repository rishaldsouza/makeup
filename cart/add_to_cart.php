<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user'])) {
    header("Location: ../login.php");
    exit;
}

$uid = (int)$_SESSION['user'];
$pid = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$from = isset($_GET['from']) ? $_GET['from'] : 'index';

if ($pid <= 0) { header("Location: ../index.php"); exit; }

// Verify product exists
$chk = $conn->prepare("SELECT id FROM products WHERE id=?");
$chk->bind_param("i", $pid);
$chk->execute();
if (!$chk->get_result()->fetch_assoc()) { header("Location: ../index.php"); exit; }

$stmt = $conn->prepare("SELECT id, qty FROM cart WHERE user_id=? AND product_id=?");
$stmt->bind_param("ii", $uid, $pid);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if ($row) {
    $newQty = (int)$row['qty'] + 1;
    $upd = $conn->prepare("UPDATE cart SET qty=? WHERE id=?");
    $upd->bind_param("ii", $newQty, $row['id']);
    $upd->execute();
} else {
    $ins = $conn->prepare("INSERT INTO cart(user_id, product_id, qty) VALUES(?,?,1)");
    $ins->bind_param("ii", $uid, $pid);
    $ins->execute();
}

$redirect = ($from === 'product') ? "../product.php?id=$pid&added=1" : "../index.php?added=1";
header("Location: $redirect");
exit;
