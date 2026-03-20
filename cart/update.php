<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user'])) { header("Location: ../login.php"); exit; }

$cart_id = (int)$_GET['id'];
$type    = $_GET['type'] ?? '';
$uid     = (int)$_SESSION['user'];

$stmt = $conn->prepare("SELECT qty FROM cart WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $cart_id, $uid);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if ($row) {
    if ($type === 'inc') {
        $qty = (int)$row['qty'] + 1;
        $conn->prepare("UPDATE cart SET qty=? WHERE id=?")->bind_param("ii", $qty, $cart_id) && $conn->prepare("UPDATE cart SET qty=? WHERE id=?")->execute();
        $u = $conn->prepare("UPDATE cart SET qty=? WHERE id=?"); $u->bind_param("ii",$qty,$cart_id); $u->execute();
    } elseif ($type === 'dec') {
        if ($row['qty'] > 1) {
            $qty = (int)$row['qty'] - 1;
            $u = $conn->prepare("UPDATE cart SET qty=? WHERE id=?"); $u->bind_param("ii",$qty,$cart_id); $u->execute();
        } else {
            $d = $conn->prepare("DELETE FROM cart WHERE id=?"); $d->bind_param("i",$cart_id); $d->execute();
        }
    } elseif ($type === 'remove') {
        $d = $conn->prepare("DELETE FROM cart WHERE id=?"); $d->bind_param("i",$cart_id); $d->execute();
    }
}

header("Location: cart.php"); exit;
