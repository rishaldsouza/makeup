<?php
session_start();
include 'config/db.php';
if (isset($_SESSION['user'])) { header("Location: index.php"); exit; }

$message = ""; $msgClass = "error";

if (isset($_GET['registered'])) { $message = "Registration successful! Please login."; $msgClass = "success"; }

if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $pass  = $_POST['password'];
    $stmt  = $conn->prepare("SELECT id, name, password FROM users WHERE email=?");
    $stmt->bind_param("s",$email); $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if ($user && password_verify($pass, $user['password'])) {
        $_SESSION['user']      = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        header("Location: index.php"); exit;
    }
    $message = "Invalid email or password."; $msgClass = "error";
}
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Beauty Canvas | Login</title>
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;1,400&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
<style>
:root{--rose:#e8547a;--rose-dark:#c93560;--rose-light:#fce4ec;--blush:#fff0f5;--text:#1a1a2e;--muted:#7b7b8e}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'DM Sans',sans-serif;background:linear-gradient(135deg,var(--blush),#ffd6e8);min-height:100vh;display:flex;align-items:center;justify-content:center;padding:20px}
.card{background:#fff;border-radius:28px;padding:48px 40px;width:100%;max-width:420px;box-shadow:0 20px 60px rgba(232,84,122,.15);text-align:center}
.logo{font-family:'Playfair Display',serif;font-size:28px;font-weight:700;color:var(--rose);margin-bottom:4px}.logo span{font-style:italic;color:var(--text)}
.subtitle{color:var(--muted);font-size:14px;margin-bottom:32px}
input{width:100%;padding:14px 18px;border:1.5px solid #fce4ec;border-radius:12px;font-size:14px;outline:none;font-family:inherit;transition:border .2s;margin-bottom:14px;background:#fafafa}
input:focus{border-color:var(--rose);background:#fff}
button{width:100%;padding:15px;background:var(--rose);color:white;border:none;border-radius:12px;font-size:16px;font-weight:600;cursor:pointer;font-family:inherit;transition:all .25s;box-shadow:0 8px 24px rgba(232,84,122,.3)}
button:hover{background:var(--rose-dark)}
.msg{padding:12px;border-radius:10px;font-size:14px;margin-bottom:18px}
.msg.error{background:#fff0f0;color:#e74c3c;border:1px solid #ffcdd2}
.msg.success{background:#f0fff4;color:#27ae60;border:1px solid #c8e6c9}
.link{margin-top:18px;font-size:14px;color:var(--muted)}.link a{color:var(--rose);font-weight:600;text-decoration:none}
.link a:hover{text-decoration:underline}
.back{display:block;margin-top:12px;color:var(--muted);font-size:13px;text-decoration:none}.back:hover{color:var(--rose)}
</style></head><body>
<div class="card">
    <div class="logo">Beauty <span>Canvas</span></div>
    <p class="subtitle">Welcome back, beautiful 💄</p>
    <?php if($message): ?><div class="msg <?=$msgClass?>"><?=htmlspecialchars($message)?></div><?php endif; ?>
    <form method="POST">
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="password" name="password" placeholder="Password" required>
        <button name="login">Login to Beauty Canvas</button>
    </form>
    <div class="link">Don't have an account? <a href="register.php">Register here</a></div>
    <a href="index.php" class="back">← Back to Home</a>
</div>
</body></html>
