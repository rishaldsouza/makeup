<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['user'])) { header("Location: ../login.php"); exit; }
$uid = (int)$_SESSION['user'];

$sql = "SELECT c.id AS cart_id, p.id AS product_id, p.name, p.price, p.image, c.qty
        FROM cart c JOIN products p ON c.product_id=p.id WHERE c.user_id=?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $uid);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
$total = 0;
while ($row = $result->fetch_assoc()) {
    $row['subtotal'] = $row['price'] * $row['qty'];
    $total += $row['subtotal'];
    $items[] = $row;
}

function cartImg($img) {
    if (!$img) return '../assets/images/lipstick.jpg';
    foreach (['../assets/images/', '../products/'] as $dir) {
        if (file_exists(__DIR__.'/'.$dir.$img)) return $dir.$img;
    }
    return '../assets/images/lipstick.jpg';
}

$delivery   = $total >= 999 ? 0 : 49;
$grandTotal = $total + $delivery;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Beauty Canvas | Your Cart</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
:root{--rose:#e8547a;--rose-dark:#c93560;--rose-light:#fce4ec;--blush:#fff0f5;--text:#1a1a2e;--muted:#7b7b8e}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'DM Sans',sans-serif;background:var(--blush);color:var(--text);min-height:100vh}
header{background:rgba(255,255,255,.95);backdrop-filter:blur(12px);border-bottom:1px solid #fce4ec;padding:0 60px;height:70px;display:flex;align-items:center;justify-content:space-between}
.logo{font-family:'Playfair Display',serif;font-size:24px;font-weight:700;color:var(--rose)}.logo span{font-style:italic;color:var(--text)}
nav a{margin-left:16px;text-decoration:none;color:var(--muted);font-size:14px;font-weight:500;padding:8px 16px;border-radius:30px;transition:all .2s}
nav a:hover{color:var(--rose);background:var(--blush)}
/* Layout */
.page{max-width:1160px;margin:40px auto;padding:0 24px;display:grid;grid-template-columns:1fr 380px;gap:28px;align-items:start}
/* Cart box */
.cart-box{background:#fff;border-radius:20px;overflow:hidden;box-shadow:0 4px 20px rgba(232,84,122,.08)}
.cart-header{padding:24px 28px;border-bottom:1px solid #fce4ec;display:flex;align-items:center;justify-content:space-between}
.cart-header h2{font-family:'Playfair Display',serif;font-size:24px}
.cart-header span{color:var(--muted);font-size:14px}
.cart-item{display:grid;grid-template-columns:80px 1fr auto;gap:16px;align-items:center;padding:20px 28px;border-bottom:1px solid #fce4ec}
.cart-item:last-child{border-bottom:none}
.cart-img{width:80px;height:80px;object-fit:cover;border-radius:10px}
.item-name{font-weight:600;font-size:15px;margin-bottom:4px}
.item-price{color:var(--muted);font-size:14px}
.qty-controls{display:flex;align-items:center;gap:10px;margin-top:10px}
.qty-btn{width:30px;height:30px;border-radius:50%;border:1.5px solid #fce4ec;background:#fff;color:var(--rose);font-size:16px;cursor:pointer;display:flex;align-items:center;justify-content:center;text-decoration:none;font-weight:700;transition:all .2s;line-height:1}
.qty-btn:hover{background:var(--rose);color:white;border-color:var(--rose)}
.qty-num{font-weight:600;font-size:15px;min-width:20px;text-align:center}
.item-subtotal{font-weight:700;font-size:16px;color:var(--rose);text-align:right}
.remove-btn{display:block;color:#ccc;font-size:12px;text-decoration:none;margin-top:6px;text-align:right;transition:color .2s}
.remove-btn:hover{color:#e74c3c}
/* Right panel */
.right-panel{display:flex;flex-direction:column;gap:20px;position:sticky;top:90px}
/* Address box */
.address-box{background:#fff;border-radius:20px;padding:24px 28px;box-shadow:0 4px 20px rgba(232,84,122,.08)}
.section-label{font-size:13px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.6px;margin-bottom:16px;display:flex;align-items:center;gap:8px}
.field-row{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-bottom:12px}
.field-row.full{grid-template-columns:1fr}
.field-group{display:flex;flex-direction:column;gap:5px}
.field-group label{font-size:12px;font-weight:600;color:var(--muted)}
.field-group input,.field-group select,.field-group textarea{padding:11px 13px;border:1.5px solid #eee;border-radius:10px;font-size:13px;font-family:inherit;outline:none;transition:border .2s;background:#fafafa;color:var(--text);width:100%}
.field-group input:focus,.field-group select:focus,.field-group textarea:focus{border-color:var(--rose);background:#fff}
.field-group input.error-field{border-color:#e74c3c}
.field-error{font-size:11px;color:#e74c3c;margin-top:2px;display:none}
/* Summary box */
.summary-box{background:#fff;border-radius:20px;padding:24px 28px;box-shadow:0 4px 20px rgba(232,84,122,.08)}
.summary-box h3{font-family:'Playfair Display',serif;font-size:20px;margin-bottom:18px}
.summary-row{display:flex;justify-content:space-between;margin-bottom:12px;font-size:14px;color:var(--muted)}
.summary-row.total{font-size:18px;font-weight:700;color:var(--text);border-top:1px solid #fce4ec;padding-top:14px;margin-top:4px}
.summary-row.total span:last-child{color:var(--rose)}
.method-label{display:flex;align-items:center;gap:10px;background:var(--blush);border:1.5px solid #fce4ec;border-radius:12px;padding:12px 14px;cursor:pointer;margin-bottom:10px;font-size:14px;font-weight:500;transition:all .2s}
.method-label input{accent-color:var(--rose)}
.method-label:has(input:checked){border-color:var(--rose);background:#fff0f5}
.checkout-btn{width:100%;padding:15px;background:var(--rose);color:white;border:none;border-radius:14px;font-size:16px;font-weight:600;cursor:pointer;margin-top:14px;transition:all .25s;box-shadow:0 8px 24px rgba(232,84,122,.3);font-family:inherit}
.checkout-btn:hover{background:var(--rose-dark);transform:translateY(-1px)}
.continue-btn{display:block;text-align:center;color:var(--muted);text-decoration:none;font-size:14px;margin-top:12px}
.continue-btn:hover{color:var(--rose)}
/* Empty */
.empty-cart{text-align:center;padding:80px 40px}
.empty-cart .icon{font-size:64px;margin-bottom:20px}
.empty-cart h3{font-family:'Playfair Display',serif;font-size:26px;margin-bottom:10px}
.empty-cart p{color:var(--muted);margin-bottom:24px}
.shop-btn{display:inline-block;padding:14px 32px;background:var(--rose);color:white;text-decoration:none;border-radius:50px;font-weight:600;transition:all .25s}
.shop-btn:hover{background:var(--rose-dark)}
@media(max-width:960px){.page{grid-template-columns:1fr}.right-panel{position:static}header{padding:0 24px}}
</style>
</head>
<body>
<header>
    <div class="logo">Beauty <span>Canvas</span></div>
    <nav>
        <a href="../index.php">Home</a>
        <a href="../index.php#products">Shop</a>
        <a href="../logout.php">Logout</a>
    </nav>
</header>

<?php if(count($items) > 0): ?>
<form action="../payment/checkout.php" method="POST" id="checkoutForm" novalidate>
<div class="page">

    <!-- LEFT: Cart Items -->
    <div class="cart-box">
        <div class="cart-header">
            <h2>Your Cart</h2>
            <span><?=count($items)?> item<?=count($items)!=1?'s':''?></span>
        </div>
        <?php foreach($items as $item): ?>
        <div class="cart-item">
            <img class="cart-img" src="<?=htmlspecialchars(cartImg($item['image']))?>" alt="<?=htmlspecialchars($item['name'])?>">
            <div>
                <div class="item-name"><?=htmlspecialchars($item['name'])?></div>
                <div class="item-price">₹<?=number_format($item['price'],2)?> each</div>
                <div class="qty-controls">
                    <a class="qty-btn" href="update.php?id=<?=$item['cart_id']?>&type=dec">−</a>
                    <span class="qty-num"><?=$item['qty']?></span>
                    <a class="qty-btn" href="update.php?id=<?=$item['cart_id']?>&type=inc">+</a>
                </div>
            </div>
            <div>
                <div class="item-subtotal">₹<?=number_format($item['subtotal'],2)?></div>
                <a class="remove-btn" href="update.php?id=<?=$item['cart_id']?>&type=remove">✕ Remove</a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- RIGHT: Address + Summary -->
    <div class="right-panel">

        <!-- Delivery Address -->
        <div class="address-box">
            <div class="section-label">📍 Delivery Address</div>

            <div class="field-row">
                <div class="field-group">
                    <label>Full Name *</label>
                    <input type="text" name="full_name" placeholder="Your full name" required>
                    <span class="field-error" id="err_full_name">Please enter your name</span>
                </div>
                <div class="field-group">
                    <label>Phone *</label>
                    <input type="tel" name="phone" placeholder="10-digit number" maxlength="10" required>
                    <span class="field-error" id="err_phone">Enter a valid phone number</span>
                </div>
            </div>

            <div class="field-row full">
                <div class="field-group">
                    <label>Address Line 1 *</label>
                    <input type="text" name="address_line1" placeholder="House no, Street, Area" required>
                    <span class="field-error" id="err_address_line1">Please enter your address</span>
                </div>
            </div>

            <div class="field-row full">
                <div class="field-group">
                    <label>Address Line 2 <span style="font-weight:400;color:#bbb">(optional)</span></label>
                    <input type="text" name="address_line2" placeholder="Landmark, Colony (optional)">
                </div>
            </div>

            <div class="field-row">
                <div class="field-group">
                    <label>City *</label>
                    <input type="text" name="city" placeholder="City" required>
                    <span class="field-error" id="err_city">Please enter your city</span>
                </div>
                <div class="field-group">
                    <label>State *</label>
                    <select name="state" required>
                        <option value="">Select State</option>
                        <?php
                        $states = ['Andhra Pradesh','Arunachal Pradesh','Assam','Bihar','Chhattisgarh','Goa','Gujarat','Haryana','Himachal Pradesh','Jharkhand','Karnataka','Kerala','Madhya Pradesh','Maharashtra','Manipur','Meghalaya','Mizoram','Nagaland','Odisha','Punjab','Rajasthan','Sikkim','Tamil Nadu','Telangana','Tripura','Uttar Pradesh','Uttarakhand','West Bengal','Delhi','Jammu & Kashmir','Ladakh','Puducherry'];
                        foreach($states as $s) echo "<option>$s</option>";
                        ?>
                    </select>
                    <span class="field-error" id="err_state">Please select your state</span>
                </div>
            </div>

            <div class="field-row">
                <div class="field-group">
                    <label>Pincode *</label>
                    <input type="text" name="pincode" placeholder="6-digit pincode" maxlength="6" required>
                    <span class="field-error" id="err_pincode">Enter valid 6-digit pincode</span>
                </div>
                <div class="field-group">
                    <label>Country</label>
                    <input type="text" name="country" value="India" readonly style="background:#f0f0f5;color:var(--muted)">
                </div>
            </div>
        </div>

        <!-- Order Summary + Payment -->
        <div class="summary-box">
            <h3>Order Summary</h3>
            <div class="summary-row"><span>Subtotal</span><span>₹<?=number_format($total,2)?></span></div>
            <div class="summary-row">
                <span>Delivery</span>
                <span style="color:<?=$delivery==0?'#27ae60':'inherit'?>"><?=$delivery==0?'Free':'₹'.number_format($delivery,2)?></span>
            </div>
            <div class="summary-row total">
                <span>Total</span>
                <span>₹<?=number_format($grandTotal,2)?></span>
            </div>

            <div style="margin:18px 0 10px;font-size:13px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.6px">💳 Payment Method</div>
            <label class="method-label"><input type="radio" name="method" value="COD" required> 🚚 Cash on Delivery</label>
            <label class="method-label"><input type="radio" name="method" value="Online"> 💳 Online Payment</label>

            <input type="hidden" name="grand_total" value="<?=$grandTotal?>">
            <button type="submit" class="checkout-btn">Place Order →</button>
            <a href="../index.php#products" class="continue-btn">← Continue Shopping</a>
        </div>

    </div>
</div>
</form>

<?php else: ?>
<div style="max-width:600px;margin:60px auto;padding:0 24px">
    <div class="cart-box empty-cart">
        <div class="icon">🛒</div>
        <h3>Your cart is empty</h3>
        <p>Add some products to get started on your beauty journey</p>
        <a href="../index.php#products" class="shop-btn">Browse Products</a>
    </div>
</div>
<?php endif; ?>

<script>
document.getElementById('checkoutForm')?.addEventListener('submit', function(e) {
    let valid = true;

    // Clear all errors
    document.querySelectorAll('.field-error').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.error-field').forEach(el => el.classList.remove('error-field'));

    const required = {
        full_name:     { el: null, msg: 'err_full_name',    check: v => v.trim().length >= 2 },
        phone:         { el: null, msg: 'err_phone',        check: v => /^\d{10}$/.test(v.trim()) },
        address_line1: { el: null, msg: 'err_address_line1',check: v => v.trim().length >= 5 },
        city:          { el: null, msg: 'err_city',         check: v => v.trim().length >= 2 },
        state:         { el: null, msg: 'err_state',        check: v => v !== '' },
        pincode:       { el: null, msg: 'err_pincode',      check: v => /^\d{6}$/.test(v.trim()) },
    };

    for (const [name, rule] of Object.entries(required)) {
        const field = document.querySelector(`[name="${name}"]`);
        if (!field || !rule.check(field.value)) {
            field?.classList.add('error-field');
            const errEl = document.getElementById(rule.msg);
            if (errEl) errEl.style.display = 'block';
            valid = false;
        }
    }

    if (!document.querySelector('input[name="method"]:checked')) {
        alert('Please select a payment method.');
        valid = false;
    }

    if (!valid) {
        e.preventDefault();
        // Scroll to first error
        const firstErr = document.querySelector('.error-field');
        firstErr?.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});

// Live pincode digits only
document.querySelector('[name="pincode"]')?.addEventListener('input', function() {
    this.value = this.value.replace(/\D/g,'');
});
document.querySelector('[name="phone"]')?.addEventListener('input', function() {
    this.value = this.value.replace(/\D/g,'');
});
</script>

</body>
</html>
