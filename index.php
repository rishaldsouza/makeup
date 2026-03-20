<?php
session_start();
include 'config/db.php';

$products = [];
$res = mysqli_query($conn, "SELECT id, name, description, price, image, category FROM products ORDER BY id DESC LIMIT 6");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) $products[] = $row;
}

function productImagePath($image) {
    if (!$image) return 'assets/images/lipstick.jpg';
    if (file_exists(__DIR__.'/assets/images/'.$image)) return 'assets/images/'.$image;
    if (file_exists(__DIR__.'/products/'.$image)) return 'products/'.$image;
    return 'assets/images/lipstick.jpg';
}

$heroImage = !empty($products) ? productImagePath($products[0]['image']) : 'assets/images/lipstick.jpg';
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
<title>Beauty Canvas | Premium Makeup & Skincare</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,700;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
:root{--rose:#e8547a;--rose-dark:#c93560;--rose-light:#fce4ec;--blush:#fff0f5;--text:#1a1a2e;--muted:#7b7b8e;--white:#ffffff;--card-shadow:0 8px 32px rgba(232,84,122,.12);--radius:18px}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'DM Sans',sans-serif;background:#fff;color:var(--text);overflow-x:hidden}
header{position:sticky;top:0;z-index:100;background:rgba(255,255,255,.95);backdrop-filter:blur(12px);border-bottom:1px solid #fce4ec;padding:0 60px;height:70px;display:flex;align-items:center;justify-content:space-between}
.logo{font-family:'Playfair Display',serif;font-size:24px;font-weight:700;color:var(--rose)}.logo span{font-style:italic;color:var(--text)}
nav{display:flex;align-items:center;gap:8px}
nav a{padding:8px 16px;text-decoration:none;color:var(--muted);font-size:14px;font-weight:500;border-radius:30px;transition:all .2s}
nav a:hover{color:var(--rose);background:var(--blush)}
.nav-cart{background:var(--rose);color:white!important;font-weight:600!important;position:relative;display:inline-flex;align-items:center;gap:6px}
.nav-cart:hover{background:var(--rose-dark)!important}
.cart-badge{background:white;color:var(--rose);font-size:11px;font-weight:700;width:20px;height:20px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center}
.hero{display:grid;grid-template-columns:1fr 1fr;min-height:calc(100vh - 70px);background:linear-gradient(135deg,var(--blush) 0%,#fff5f8 60%,#fff 100%);position:relative;overflow:hidden}
.hero::before{content:'';position:absolute;width:500px;height:500px;background:radial-gradient(circle,rgba(232,84,122,.08) 0%,transparent 70%);top:-100px;right:-100px;border-radius:50%}
.hero-text{display:flex;flex-direction:column;justify-content:center;padding:80px 60px;position:relative;z-index:2}
.hero-badge{display:inline-flex;align-items:center;gap:8px;background:var(--rose-light);color:var(--rose);font-size:12px;font-weight:600;padding:6px 14px;border-radius:20px;width:fit-content;margin-bottom:24px;letter-spacing:.5px;text-transform:uppercase}
.hero-text h1{font-family:'Playfair Display',serif;font-size:clamp(36px,5vw,58px);line-height:1.15;color:var(--text);margin-bottom:20px}
.hero-text h1 em{font-style:italic;color:var(--rose)}
.hero-text p{color:var(--muted);font-size:16px;line-height:1.7;max-width:440px;margin-bottom:36px}
.hero-actions{display:flex;gap:14px;flex-wrap:wrap}
.btn-primary{display:inline-flex;align-items:center;gap:8px;padding:14px 30px;background:var(--rose);color:white;text-decoration:none;border-radius:50px;font-weight:600;font-size:15px;transition:all .25s;box-shadow:0 8px 24px rgba(232,84,122,.35)}
.btn-primary:hover{background:var(--rose-dark);transform:translateY(-2px);box-shadow:0 12px 30px rgba(232,84,122,.4)}
.btn-outline{display:inline-flex;align-items:center;gap:8px;padding:14px 30px;border:2px solid var(--rose-light);color:var(--rose);text-decoration:none;border-radius:50px;font-weight:600;font-size:15px;transition:all .25s;background:white}
.btn-outline:hover{border-color:var(--rose);background:var(--blush)}
.hero-stats{display:flex;gap:32px;margin-top:48px;padding-top:28px;border-top:1px solid #fce4ec}
.stat-item{text-align:center}.stat-item strong{display:block;font-size:22px;font-weight:700;color:var(--text)}.stat-item span{font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.5px}
.hero-image{display:flex;align-items:center;justify-content:center;padding:60px 40px 60px 0;position:relative;z-index:2}
.hero-image-wrap{position:relative;width:480px;height:520px}
.hero-image-wrap img{width:100%;height:100%;object-fit:cover;border-radius:40% 60% 60% 40% / 40% 40% 60% 60%;box-shadow:0 30px 80px rgba(232,84,122,.2)}
.hero-float-badge{position:absolute;background:white;border-radius:14px;padding:12px 18px;box-shadow:0 10px 30px rgba(0,0,0,.12);display:flex;align-items:center;gap:10px;font-size:13px;font-weight:600}
.hero-float-badge.b1{top:40px;left:-20px}.hero-float-badge.b2{bottom:80px;right:-20px}
.badge-icon{font-size:22px}
.features{background:var(--blush);padding:50px 60px;display:grid;grid-template-columns:repeat(4,1fr);gap:30px}
.feature-item{text-align:center}.feature-item .icon{font-size:32px;margin-bottom:12px}
.feature-item h4{font-size:15px;font-weight:600;color:var(--text);margin-bottom:6px}
.feature-item p{font-size:13px;color:var(--muted)}
.categories{padding:50px 60px;background:#fff}
.categories h2{font-family:'Playfair Display',serif;font-size:28px;margin-bottom:28px;color:var(--text)}
.cat-grid{display:flex;gap:16px;flex-wrap:wrap}
.cat-chip{padding:10px 22px;border-radius:30px;border:1.5px solid #fce4ec;text-decoration:none;color:var(--muted);font-size:14px;font-weight:500;transition:all .2s;background:#fff}
.cat-chip:hover,.cat-chip.active{background:var(--rose);color:white;border-color:var(--rose)}
.products-section{padding:0 60px 80px}
.section-header{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:36px}
.section-header h2{font-family:'Playfair Display',serif;font-size:34px;color:var(--text)}
.section-header p{color:var(--muted);font-size:14px;margin-top:4px}
.view-all{color:var(--rose);text-decoration:none;font-weight:600;font-size:14px;padding:8px 18px;border:1.5px solid var(--rose-light);border-radius:20px;transition:all .2s}
.view-all:hover{background:var(--blush)}
.products-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px}
.product-card{background:#fff;border-radius:var(--radius);border:1px solid #fce4ec;overflow:hidden;transition:all .3s cubic-bezier(.4,0,.2,1);position:relative}
.product-card:hover{transform:translateY(-6px);box-shadow:var(--card-shadow);border-color:rgba(232,84,122,.2)}
.product-img-wrap{position:relative;height:240px;overflow:hidden;background:var(--blush)}
.product-img-wrap img{width:100%;height:100%;object-fit:cover;transition:transform .4s ease}
.product-card:hover .product-img-wrap img{transform:scale(1.06)}
.product-category{position:absolute;top:12px;left:12px;background:white;color:var(--rose);font-size:11px;font-weight:600;padding:4px 10px;border-radius:20px;text-transform:uppercase;letter-spacing:.4px}
.wishlist-btn{position:absolute;top:12px;right:12px;width:36px;height:36px;background:white;border-radius:50%;display:flex;align-items:center;justify-content:center;text-decoration:none;font-size:16px;box-shadow:0 2px 8px rgba(0,0,0,.1);transition:all .2s}
.wishlist-btn:hover{transform:scale(1.15);background:var(--rose-light)}
.product-info{padding:18px}
.product-info h3{font-size:15px;font-weight:600;color:var(--text);margin-bottom:6px}
.product-price{font-size:18px;font-weight:700;color:var(--rose);margin-bottom:14px}
.product-actions{display:flex;gap:10px}
.add-cart-btn{flex:1;padding:10px;background:var(--rose);color:white;border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;text-align:center;transition:all .2s}
.add-cart-btn:hover{background:var(--rose-dark)}
.view-btn{padding:10px 16px;background:var(--blush);color:var(--rose);border:none;border-radius:10px;font-size:13px;font-weight:600;cursor:pointer;text-decoration:none;text-align:center;transition:all .2s}
.view-btn:hover{background:var(--rose-light)}
.testimonials-section{padding:60px 60px}
.testimonials-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:24px;margin-top:36px}
.testimonial-card{background:var(--blush);border-radius:var(--radius);padding:28px}
.stars{color:#f5a623;font-size:18px;margin-bottom:12px}
.testimonial-card p{font-size:14px;line-height:1.7;color:var(--text);font-style:italic;margin-bottom:16px}
.testimonial-author{display:flex;align-items:center;gap:12px}
.author-avatar{width:38px;height:38px;background:var(--rose);border-radius:50%;display:flex;align-items:center;justify-content:center;color:white;font-weight:700;font-size:14px}
.author-info strong{display:block;font-size:13px;font-weight:600}.author-info span{font-size:12px;color:var(--muted)}
footer{background:var(--text);color:#ccc;padding:50px 60px 30px}
.footer-grid{display:grid;grid-template-columns:2fr 1fr 1fr 1fr;gap:40px;margin-bottom:40px}
.footer-brand p{font-size:13px;margin-top:12px;line-height:1.7;color:#888}
.footer-col h4{color:white;font-size:14px;font-weight:600;margin-bottom:16px}
.footer-col a{display:block;color:#888;text-decoration:none;font-size:13px;margin-bottom:10px;transition:color .2s}
.footer-col a:hover{color:var(--rose)}
.footer-bottom{border-top:1px solid #333;padding-top:24px;text-align:center;font-size:13px;color:#666}
.flash{position:fixed;top:80px;right:24px;background:white;border-left:4px solid var(--rose);border-radius:10px;padding:14px 20px;box-shadow:0 8px 24px rgba(0,0,0,.12);font-size:14px;font-weight:500;color:var(--text);z-index:9999;animation:slideIn .3s ease;display:none}
@keyframes slideIn{from{opacity:0;transform:translateX(30px)}to{opacity:1;transform:translateX(0)}}
.empty-products{grid-column:1/-1;text-align:center;padding:60px 20px;color:var(--muted)}
@media(max-width:900px){header{padding:0 24px}.hero{grid-template-columns:1fr}.hero-image{display:none}.hero-text{padding:60px 24px}.products-grid{grid-template-columns:repeat(2,1fr)}.features{grid-template-columns:repeat(2,1fr)}.testimonials-grid{grid-template-columns:1fr}.footer-grid{grid-template-columns:1fr 1fr}.products-section,.categories,.testimonials-section{padding-left:24px;padding-right:24px}}
@media(max-width:600px){.products-grid{grid-template-columns:1fr}.features{grid-template-columns:1fr;padding:30px 24px}.footer-grid{grid-template-columns:1fr}}
</style>
</head>
<body>
<div class="flash" id="flash">✓ Added to cart!</div>
<header>
    <div class="logo">Beauty <span>Canvas</span></div>
    <nav>
        <a href="index.php">Home</a>
        <a href="#products">Shop</a>
        <?php if(isset($_SESSION['user'])): ?>
            <a href="cart/cart.php" class="nav-cart">🛒 Cart <?php if($cartCount>0): ?><span class="cart-badge"><?=$cartCount?></span><?php endif; ?></a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
    </nav>
</header>

<section class="hero">
    <div class="hero-text">
        <div class="hero-badge">✨ New Collection 2026</div>
        <h1>Radiant Beauty,<br><em>Naturally Yours</em></h1>
        <p>Discover premium beauty products crafted to enhance your natural glow. Feel confident, radiant, and beautifully yourself — every single day.</p>
        <div class="hero-actions">
            <a href="#products" class="btn-primary">Shop Now →</a>
            <a href="cart/cart.php" class="btn-outline">🛒 View Cart</a>
        </div>
        <div class="hero-stats">
            <div class="stat-item"><strong>500+</strong><span>Products</span></div>
            <div class="stat-item"><strong>10K+</strong><span>Customers</span></div>
            <div class="stat-item"><strong>4.9★</strong><span>Rating</span></div>
        </div>
    </div>
    <div class="hero-image">
        <div class="hero-image-wrap">
            <img src="<?=htmlspecialchars($heroImage)?>" alt="Beauty Hero">
            <div class="hero-float-badge b1"><span class="badge-icon">🌿</span><div><strong style="font-size:12px">Cruelty Free</strong><br><span style="font-size:11px;color:var(--muted)">100% Vegan</span></div></div>
            <div class="hero-float-badge b2"><span class="badge-icon">⭐</span><div><strong style="font-size:12px">Top Rated</strong><br><span style="font-size:11px;color:var(--muted)">4.9 / 5 Stars</span></div></div>
        </div>
    </div>
</section>

<div class="features">
    <div class="feature-item"><div class="icon">🚚</div><h4>Free Delivery</h4><p>On orders above ₹999</p></div>
    <div class="feature-item"><div class="icon">🔄</div><h4>Easy Returns</h4><p>30-day return policy</p></div>
    <div class="feature-item"><div class="icon">🔒</div><h4>Secure Payment</h4><p>Safe & encrypted checkout</p></div>
    <div class="feature-item"><div class="icon">💬</div><h4>24/7 Support</h4><p>Always here for you</p></div>
</div>

<section class="categories">
    <h2>Shop by Category</h2>
    <div class="cat-grid">
        <a href="#products" class="cat-chip active">All Products</a>
        <a href="#products" class="cat-chip">💄 Lips</a>
        <a href="#products" class="cat-chip">✨ Skincare</a>
        <a href="#products" class="cat-chip">🌸 Fragrance</a>
        <a href="#products" class="cat-chip">💅 Nails</a>
        <a href="#products" class="cat-chip">👁 Eyes</a>
    </div>
</section>

<section class="products-section" id="products">
    <div class="section-header">
        <div><h2>Hand-Picked Products</h2><p>Our finest selection, chosen just for you</p></div>
        <a href="#products" class="view-all">View All →</a>
    </div>
    <div class="products-grid">
        <?php if(!empty($products)): ?>
            <?php foreach($products as $p): ?>
            <div class="product-card">
                <div class="product-img-wrap">
                    <img src="<?=htmlspecialchars(productImagePath($p['image']))?>" alt="<?=htmlspecialchars($p['name'])?>">
                    <span class="product-category"><?=htmlspecialchars($p['category']??'Beauty')?></span>
                    <?php if(isset($_SESSION['user'])): ?>
                    <a href="cart/wishlist.php?id=<?=(int)$p['id']?>" class="wishlist-btn" title="Wishlist">🤍</a>
                    <?php endif; ?>
                </div>
                <div class="product-info">
                    <h3><?=htmlspecialchars($p['name'])?></h3>
                    <div class="product-price">₹<?=number_format((float)$p['price'],2)?></div>
                    <div class="product-actions">
                        <a class="add-cart-btn" href="cart/add_to_cart.php?id=<?=(int)$p['id']?>">🛒 Add to Cart</a>
                        <a class="view-btn" href="product.php?id=<?=(int)$p['id']?>">View</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-products"><div style="font-size:50px;margin-bottom:16px">🛍</div><h3>No products yet</h3><p>Add products from the admin panel.</p></div>
        <?php endif; ?>
    </div>
</section>

<section class="testimonials-section">
    <div class="section-header"><div><h2>What Our Customers Say</h2><p>Real reviews from real beauty lovers</p></div></div>
    <div class="testimonials-grid">
        <div class="testimonial-card"><div class="stars">★★★★★</div><p>"Absolutely love the Night Serum! My skin has never felt so smooth and glowing."</p><div class="testimonial-author"><div class="author-avatar">P</div><div class="author-info"><strong>Priya Sharma</strong><span>Verified Buyer</span></div></div></div>
        <div class="testimonial-card"><div class="stars">★★★★★</div><p>"The lipstick collection is stunning! Great pigmentation, long-lasting and luxurious."</p><div class="testimonial-author"><div class="author-avatar">A</div><div class="author-info"><strong>Ananya Reddy</strong><span>Verified Buyer</span></div></div></div>
        <div class="testimonial-card"><div class="stars">★★★★☆</div><p>"Fast delivery, beautiful products! The perfume smells heavenly. My go-to beauty store."</p><div class="testimonial-author"><div class="author-avatar">M</div><div class="author-info"><strong>Meena Krishnan</strong><span>Verified Buyer</span></div></div></div>
    </div>
</section>

<footer>
    <div class="footer-grid">
        <div class="footer-brand"><div class="logo" style="color:white">Beauty <span style="color:var(--rose)">Canvas</span></div><p>Premium beauty products crafted for every skin type. Look your best, feel your best — every day.</p></div>
        <div class="footer-col"><h4>Quick Links</h4><a href="index.php">Home</a><a href="#products">Shop</a><a href="cart/cart.php">Cart</a></div>
        <div class="footer-col"><h4>Account</h4><a href="login.php">Login</a><a href="register.php">Register</a></div>
        <div class="footer-col"><h4>Support</h4><a href="#">FAQs</a><a href="#">Contact Us</a><a href="#">Return Policy</a></div>
    </div>
    <div class="footer-bottom">&copy; 2026 Beauty Canvas. All rights reserved.</div>
</footer>
<?php if(isset($_GET['added'])): ?>
<script>const f=document.getElementById('flash');f.style.display='block';setTimeout(()=>f.style.display='none',3000);</script>
<?php endif; ?>
</body>
</html>
