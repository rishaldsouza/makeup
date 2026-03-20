<?php
session_start();
include '../config/db.php';
$message = ''; $msgClass = 'error';

if (isset($_POST['register'])) {
    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);
    $pass  = $_POST['password'];
    if (!$name || !$email || !$pass) { $message = "All fields are required."; }
    else {
        $chk = $conn->prepare("SELECT id FROM admin WHERE email=?"); $chk->bind_param("s",$email); $chk->execute();
        if ($chk->get_result()->fetch_assoc()) { $message = "Email already registered."; }
        else {
            $hash = password_hash($pass, PASSWORD_BCRYPT);
            $ins  = $conn->prepare("INSERT INTO admin(name,email,password) VALUES(?,?,?)");
            $ins->bind_param("sss",$name,$email,$hash);
            if ($ins->execute()) { $message = "Admin registered! You can now login."; $msgClass = 'success'; }
            else { $message = "Registration failed."; }
        }
    }
}
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Admin Register | Beauty Canvas</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{--rose:#e8547a;--rose-dark:#c93560;--text:#1a1a2e;--muted:#7b7b8e}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'DM Sans',sans-serif;background:#f8f9fc;min-height:100vh;display:flex;align-items:center;justify-content:center}
.card{background:#fff;border-radius:20px;padding:44px 40px;width:100%;max-width:400px;box-shadow:0 8px 32px rgba(0,0,0,.08);text-align:center}
.logo{font-size:20px;font-weight:700;color:var(--text);margin-bottom:4px}.logo span{color:var(--rose)}
.subtitle{color:var(--muted);font-size:14px;margin-bottom:28px}
input{width:100%;padding:13px 16px;border:1.5px solid #e0e0e0;border-radius:10px;font-size:14px;font-family:inherit;outline:none;transition:border .2s;margin-bottom:14px}
input:focus{border-color:var(--rose)}
button{width:100%;padding:14px;background:var(--rose);color:white;border:none;border-radius:10px;font-size:15px;font-weight:600;cursor:pointer;font-family:inherit}
button:hover{background:var(--rose-dark)}
.msg{padding:12px;border-radius:8px;font-size:14px;margin-bottom:16px}
.msg.error{background:#ffebee;color:#c62828}.msg.success{background:#e8f5e9;color:#2e7d32}
.link{margin-top:14px;font-size:13px;color:var(--muted)}.link a{color:var(--rose);font-weight:600;text-decoration:none}
</style></head><body>
<div class="card">
    <div class="logo">Beauty <span>Canvas</span></div>
    <p class="subtitle">Register Admin Account</p>
    <?php if($message): ?><div class="msg <?=$msgClass?>"><?=htmlspecialchars($message)?></div><?php endif; ?>
    <form method="POST">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="email" name="email" placeholder="Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button name="register">Register Admin</button>
    </form>
    <div class="link"><a href="login.php">← Back to Login</a></div>
</div>
</body></html>
