<?php
session_start();
include 'config/db.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) { header('Location: index.php#products'); exit; }

$stmt = $conn->prepare('SELECT id, name, price, image, description, category FROM products WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$product = $stmt->get_result()->fetch_assoc();
if (!$product) { header('Location: index.php#products'); exit; }

function productImagePath($image) {
    if (!$image) return 'assets/images/lipstick.jpg';
    if (file_exists(__DIR__.'/assets/images/'.$image)) return 'assets/images/'.$image;
    if (file_exists(__DIR__.'/products/'.$image)) return 'products/'.$image;
    return 'assets/images/lipstick.jpg';
}

// Cart count
$cartCount = 0;
if (isset($_SESSION['user'])) {
    $uid = (int)$_SESSION['user'];
    $cr = mysqli_query($conn, "SELECT SUM(qty) as total FROM cart WHERE user_id=$uid");
    $crow = mysqli_fetch_assoc($cr);
    $cartCount = (int)($crow['total'] ?? 0);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Beauty Canvas | <?=htmlspecialchars($product['name'])?></title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
:root{--rose:#e8547a;--rose-dark:#c93560;--rose-light:#fce4ec;--blush:#fff0f5;--text:#1a1a2e;--muted:#7b7b8e}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'DM Sans',sans-serif;background:var(--blush);color:var(--text)}
header{background:rgba(255,255,255,.95);backdrop-filter:blur(12px);border-bottom:1px solid #fce4ec;padding:0 60px;height:70px;display:flex;align-items:center;justify-content:space-between}
.logo{font-family:'Playfair Display',serif;font-size:24px;font-weight:700;color:var(--rose)}.logo span{font-style:italic;color:var(--text)}
nav{display:flex;align-items:center;gap:8px}
nav a{padding:8px 16px;text-decoration:none;color:var(--muted);font-size:14px;font-weight:500;border-radius:30px;transition:all .2s}
nav a:hover{color:var(--rose);background:var(--blush)}
.nav-cart{background:var(--rose);color:white!important;font-weight:600!important;display:inline-flex;align-items:center;gap:6px}
.nav-cart:hover{background:var(--rose-dark)!important}
.cart-badge{background:white;color:var(--rose);font-size:11px;font-weight:700;width:20px;height:20px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center}
.container{max-width:1000px;margin:40px auto;background:#fff;border-radius:24px;padding:40px;box-shadow:0 10px 40px rgba(232,84,122,.1);display:grid;grid-template-columns:1fr 1fr;gap:40px}
.product-img img{width:100%;height:420px;object-fit:cover;border-radius:18px}
.product-details{display:flex;flex-direction:column;justify-content:center}
.product-cat{display:inline-block;background:var(--rose-light);color:var(--rose);font-size:12px;font-weight:600;padding:4px 12px;border-radius:20px;margin-bottom:16px;text-transform:uppercase;letter-spacing:.5px}
.product-details h1{font-family:'Playfair Display',serif;font-size:32px;color:var(--text);margin-bottom:12px}
.product-price{font-size:28px;font-weight:700;color:var(--rose);margin-bottom:20px}
.product-desc{color:var(--muted);font-size:15px;line-height:1.7;margin-bottom:28px;padding:16px;background:var(--blush);border-radius:12px}
.product-perks{display:flex;flex-direction:column;gap:10px;margin-bottom:28px}
.perk{display:flex;align-items:center;gap:10px;font-size:14px;color:var(--text)}
.perk span:first-child{font-size:18px}
.product-actions{display:flex;gap:12px}
.btn-primary{flex:1;padding:14px;background:var(--rose);color:white;text-decoration:none;border-radius:12px;font-weight:600;font-size:15px;text-align:center;transition:all .2s;border:none;cursor:pointer}
.btn-primary:hover{background:var(--rose-dark)}
.btn-outline{padding:14px 20px;border:2px solid var(--rose-light);color:var(--rose);text-decoration:none;border-radius:12px;font-weight:600;font-size:15px;text-align:center;transition:all .2s;background:white}
.btn-outline:hover{border-color:var(--rose);background:var(--blush)}
.back-link{display:block;text-align:center;color:var(--muted);text-decoration:none;margin-top:16px;font-size:14px}
.back-link:hover{color:var(--rose)}
.login-note{background:var(--blush);border:1px solid var(--rose-light);border-radius:10px;padding:12px 16px;font-size:14px;color:var(--muted);margin-top:16px}
.login-note a{color:var(--rose);font-weight:600;text-decoration:none}
@media(max-width:768px){header{padding:0 24px}.container{grid-template-columns:1fr;margin:20px;padding:24px}}
</style>
</head>
<body>
<header>
    <div class="logo">Beauty <span>Canvas</span></div>
    <nav>
        <a href="index.php">Home</a>
        <a href="index.php#products">Shop</a>
        <?php if(isset($_SESSION['user'])): ?>
            <a href="cart/cart.php" class="nav-cart">🛒 Cart <?php if($cartCount>0): ?><span class="cart-badge"><?=$cartCount?></span><?php endif; ?></a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
        <?php endif; ?>
    </nav>
</header>

<div class="container">
    <div class="product-img">
        <img src="<?=htmlspecialchars(productImagePath($product['image']))?>" alt="<?=htmlspecialchars($product['name'])?>">
    </div>
    <div class="product-details">
        <span class="product-cat"><?=htmlspecialchars($product['category']??'Beauty')?></span>
        <h1><?=htmlspecialchars($product['name'])?></h1>
        <div class="product-price">₹<?=number_format((float)$product['price'],2)?></div>
        <?php if(!empty($product['description'])): ?>
        <div class="product-desc"><?=htmlspecialchars($product['description'])?></div>
        <?php endif; ?>
        <div class="product-perks">
            <div class="perk"><span>🚚</span><span>Free delivery on orders above ₹999</span></div>
            <div class="perk"><span>🌿</span><span>100% cruelty-free & vegan</span></div>
            <div class="perk"><span>🔄</span><span>30-day easy returns</span></div>
        </div>
        <?php if(isset($_SESSION['user'])): ?>
        <div class="product-actions">
            <a class="btn-primary" href="cart/add_to_cart.php?id=<?=(int)$product['id']?>">🛒 Add to Cart</a>
            <a class="btn-outline" href="cart/wishlist.php?id=<?=(int)$product['id']?>">🤍</a>
        </div>
        <?php else: ?>
        <div class="login-note">Please <a href="login.php">login</a> to add this item to your cart.</div>
        <?php endif; ?>
        <a href="index.php#products" class="back-link">← Back to Products</a>
    </div>
</div>
</body>
</html>
