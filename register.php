<?php
include 'config/db.php';
$message = ""; $msgClass = "error";

if (isset($_POST['register'])) {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pass  = $_POST['password'];
    $conf  = $_POST['confirm'];
    if (!$name || !$email || !$pass) { $message = "All fields are required."; }
    elseif ($pass !== $conf) { $message = "Passwords do not match."; }
    else {
        $chk = $conn->prepare("SELECT id FROM users WHERE email=?"); $chk->bind_param("s",$email); $chk->execute();
        if ($chk->get_result()->fetch_assoc()) { $message = "Email already registered."; }
        else {
            $hash = password_hash($pass, PASSWORD_BCRYPT);
            $ins  = $conn->prepare("INSERT INTO users(name,email,password) VALUES(?,?,?)");
            $ins->bind_param("sss",$name,$email,$hash);
            if ($ins->execute()) { header("Location: login.php?registered=1"); exit; }
            $message = "Registration failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Beauty Canvas | Register</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
:root{--rose:#e8547a;--rose-dark:#c93560;--rose-light:#fce4ec;--blush:#fff0f5;--text:#1a1a2e;--muted:#7b7b8e}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'DM Sans',sans-serif;background:linear-gradient(135deg,#ffd6e8,var(--blush));min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
.card{background:#fff;border-radius:28px;padding:48px 40px;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(232,84,122,.15);text-align:center}
.logo{font-family:'Playfair Display',serif;font-size:28px;font-weight:700;color:var(--rose);margin-bottom:4px}.logo span{font-style:italic;color:var(--text)}
.subtitle{color:var(--muted);font-size:14px;margin-bottom:32px}
input{width:100%;padding:14px 18px;border:1.5px solid #fce4ec;border-radius:12px;font-size:14px;outline:none;font-family:inherit;transition:border .2s;margin-bottom:14px;background:#fafafa}
input:focus{border-color:var(--rose);background:#fff}
button{width:100%;padding:15px;background:var(--rose);color:white;border:none;border-radius:12px;font-size:16px;font-weight:600;cursor:pointer;font-family:inherit;transition:all .25s;box-shadow:0 8px 24px rgba(232,84,122,.3)}
button:hover{background:var(--rose-dark)}
.msg{padding:12px;border-radius:10px;font-size:14px;margin-bottom:18px;background:#fff0f0;color:#e74c3c;border:1px solid #ffcdd2}
.link{margin-top:18px;font-size:14px;color:var(--muted)}.link a{color:var(--rose);font-weight:600;text-decoration:none}
.back{display:block;margin-top:12px;color:var(--muted);font-size:13px;text-decoration:none}.back:hover{color:var(--rose)}
</style></head><body>
<div class="card">
    <div class="logo">Beauty <span>Canvas</span></div>
    <p class="subtitle">Create your beauty account ✨</p>
    <?php if($message): ?><div class="msg"><?=htmlspecialchars($message)?></div><?php endif; ?>
    <form method="POST">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="confirm" placeholder="Confirm Password" required>
        <button name="register">Create Account</button>
    </form>
    <div class="link">Already have an account? <a href="login.php">Login here</a></div>
    <a href="index.php" class="back">← Back to Home</a>
</div>
</body></html>
