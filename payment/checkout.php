<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user'])) { header("Location: ../login.php"); exit; }
if (!isset($_POST['method'])) { header("Location: ../cart/cart.php"); exit; }

$uid    = (int)$_SESSION['user'];
$method = in_array($_POST['method'], ['COD','Online']) ? $_POST['method'] : 'COD';

// Sanitise address
$full_name     = trim(htmlspecialchars($_POST['full_name']     ?? ''));
$phone         = preg_replace('/\D/', '', $_POST['phone']      ?? '');
$address_line1 = trim(htmlspecialchars($_POST['address_line1'] ?? ''));
$address_line2 = trim(htmlspecialchars($_POST['address_line2'] ?? ''));
$city          = trim(htmlspecialchars($_POST['city']          ?? ''));
$state         = trim(htmlspecialchars($_POST['state']         ?? ''));
$pincode       = preg_replace('/\D/', '', $_POST['pincode']    ?? '');

// Server-side validation
if (!$full_name || strlen($phone)!==10 || !$address_line1 || !$city || !$state || strlen($pincode)!==6) {
    header("Location: ../cart/cart.php?addr_error=1");
    exit;
}

$full_address = $address_line1;
if ($address_line2) $full_address .= ', '.$address_line2;
$full_address .= ', '.$city.', '.$state.' - '.$pincode.', India';

// Fetch cart
$cart = $conn->prepare("SELECT p.id as product_id, p.name, p.price, c.qty FROM cart c JOIN products p ON c.product_id=p.id WHERE c.user_id=?");
$cart->bind_param("i", $uid);
$cart->execute();
$result    = $cart->get_result();
$cartItems = [];
$total     = 0;
while ($r = $result->fetch_assoc()) {
    $cartItems[] = $r;
    $total += $r['price'] * $r['qty'];
}
$cart->close();

if ($total <= 0 || empty($cartItems)) { header("Location: ../cart/cart.php"); exit; }

$delivery       = $total >= 999 ? 0 : 49;
$grandTotal     = $total + $delivery;
$payment_status = ($method === 'COD') ? 'Cash on Delivery' : 'Paid';

// Insert order
$ins = $conn->prepare("INSERT INTO orders (user_id, total, payment_method, payment_status, recipient_name, phone, delivery_address) VALUES (?,?,?,?,?,?,?)");
$ins->bind_param("idsssss", $uid, $grandTotal, $method, $payment_status, $full_name, $phone, $full_address);
$ins->execute();
$order_id = $conn->insert_id;
$ins->close();

// Insert order items
$ins_item = $conn->prepare("INSERT INTO order_items (order_id, product_id, qty, price) VALUES (?,?,?,?)");
foreach ($cartItems as $item) {
    $ins_item->bind_param("iiid", $order_id, $item['product_id'], $item['qty'], $item['price']);
    $ins_item->execute();
}
$ins_item->close();

// Clear cart
$del = $conn->prepare("DELETE FROM cart WHERE user_id=?");
$del->bind_param("i", $uid);
$del->execute();
$del->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Beauty Canvas | Order Placed!</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
:root{--rose:#e8547a;--rose-dark:#c93560;--rose-light:#fce4ec;--blush:#fff0f5;--text:#1a1a2e;--muted:#7b7b8e}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'DM Sans',sans-serif;background:var(--blush);min-height:100vh;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:40px 24px}
.success-card{background:#fff;border-radius:28px;padding:48px;max-width:540px;width:100%;text-align:center;box-shadow:0 20px 60px rgba(232,84,122,.15)}
.check-icon{width:80px;height:80px;background:linear-gradient(135deg,var(--rose),var(--rose-dark));border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:36px;margin:0 auto 24px;animation:pop .4s cubic-bezier(.36,.07,.19,.97)}
@keyframes pop{0%{transform:scale(0)}80%{transform:scale(1.15)}100%{transform:scale(1)}}
h1{font-family:'Playfair Display',serif;font-size:30px;margin-bottom:8px;color:var(--text)}
.subtitle{color:var(--muted);font-size:15px;margin-bottom:8px}
.order-id{font-size:12px;color:#bbb;margin-bottom:24px;font-weight:500;letter-spacing:.8px;text-transform:uppercase}
.detail-card,.addr-card{background:var(--blush);border-radius:16px;padding:20px 22px;margin-bottom:16px;text-align:left}
.section-title{font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.6px;margin-bottom:12px}
.detail-row{display:flex;justify-content:space-between;padding:8px 0;border-bottom:1px solid #fce4ec;font-size:14px}
.detail-row:last-child{border:none;padding-bottom:0}
.detail-row strong{color:var(--text);font-weight:600}
.detail-row span{color:var(--muted)}
.detail-row.highlight{font-weight:700}
.detail-row.highlight strong{color:var(--text)}
.detail-row.highlight .val{color:var(--rose);font-weight:700}
.addr-name{font-weight:700;font-size:15px;color:var(--text);margin-bottom:4px}
.addr-text{font-size:13px;color:var(--muted);line-height:1.7}
.addr-phone{font-size:13px;color:var(--text);margin-top:8px;font-weight:500}
.btn-primary{display:inline-block;padding:14px 32px;background:var(--rose);color:white;text-decoration:none;border-radius:50px;font-weight:600;font-size:15px;margin-top:4px;transition:all .25s;box-shadow:0 8px 24px rgba(232,84,122,.3)}
.btn-primary:hover{background:var(--rose-dark)}
</style>
</head>
<body>
<div class="success-card">
    <div class="check-icon">✓</div>
    <h1>Order Placed!</h1>
    <p class="subtitle">Thank you, <?=htmlspecialchars($full_name)?>! We'll deliver it soon. 🚚</p>
    <div class="order-id">Order #<?=str_pad($order_id,6,'0',STR_PAD_LEFT)?></div>

    <div class="detail-card">
        <div class="section-title">💰 Payment Details</div>
        <div class="detail-row"><strong>Items Total</strong><span>₹<?=number_format($total,2)?></span></div>
        <div class="detail-row"><strong>Delivery</strong><span><?=$delivery==0?'<span style="color:#27ae60">Free</span>':'₹'.number_format($delivery,2)?></span></div>
        <div class="detail-row highlight"><strong>Grand Total</strong><span class="val">₹<?=number_format($grandTotal,2)?></span></div>
        <div class="detail-row"><strong>Method</strong><span><?=htmlspecialchars($method)?></span></div>
        <div class="detail-row"><strong>Status</strong><span><?=htmlspecialchars($payment_status)?></span></div>
    </div>

    <div class="addr-card">
        <div class="section-title">📍 Delivering To</div>
        <div class="addr-name"><?=htmlspecialchars($full_name)?></div>
        <div class="addr-text"><?=htmlspecialchars($full_address)?></div>
        <div class="addr-phone">📞 +91 <?=htmlspecialchars($phone)?></div>
    </div>

    <a href="../index.php" class="btn-primary">Continue Shopping →</a>
</div>
</body>
</html>