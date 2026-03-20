<?php
session_start();
include '../config/db.php';
if (isset($_SESSION['admin'])) { header("Location: index.php"); exit; }

$message = '';
if (isset($_POST['login'])) {
    $email = trim($_POST['email']);
    $pass  = $_POST['password'];
    $stmt  = $conn->prepare("SELECT * FROM admin WHERE email=?");
    $stmt->bind_param("s",$email); $stmt->execute();
    $admin = $stmt->get_result()->fetch_assoc();
    if ($admin && $pass === $admin['password']) {
        $_SESSION['admin'] = $admin['id'];
        header("Location: index.php"); exit;
    }
    $message = "Invalid email or password.";
}
?>
<!DOCTYPE html><html lang="en"><head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Admin Login | Beauty Canvas</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{--rose:#e8547a;--rose-dark:#c93560;--text:#1a1a2e;--muted:#7b7b8e}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'DM Sans',sans-serif;background:#f8f9fc;min-height:100vh;display:flex;align-items:center;justify-content:center}
.card{background:#fff;border-radius:20px;padding:44px 40px;width:100%;max-width:400px;box-shadow:0 8px 32px rgba(0,0,0,.08);text-align:center}
.logo{font-size:20px;font-weight:700;color:var(--text);margin-bottom:4px}
.logo span{color:var(--rose)}
.subtitle{color:var(--muted);font-size:14px;margin-bottom:28px}
input{width:100%;padding:13px 16px;border:1.5px solid #e0e0e0;border-radius:10px;font-size:14px;font-family:inherit;outline:none;transition:border .2s;margin-bottom:14px}
input:focus{border-color:var(--rose)}
button{width:100%;padding:14px;background:var(--rose);color:white;border:none;border-radius:10px;font-size:15px;font-weight:600;cursor:pointer;font-family:inherit;transition:all .25s}
button:hover{background:var(--rose-dark)}
.msg{background:#ffebee;color:#c62828;padding:12px;border-radius:8px;font-size:14px;margin-bottom:16px}
.back{display:block;margin-top:14px;color:var(--muted);font-size:13px;text-decoration:none}.back:hover{color:var(--rose)}
</style></head><body>
<div class="card">
    <div class="logo">Beauty <span>Canvas</span></div>
    <p class="subtitle">Admin Panel</p>
    <?php if($message): ?><div class="msg"><?=htmlspecialchars($message)?></div><?php endif; ?>
    <form method="POST">
        <input type="email" name="email" placeholder="Admin Email" required>
        <input type="password" name="password" placeholder="Password" required>
        <button name="login">Login to Admin</button>
    </form>
    <a href="../index.php" class="back">← Back to Store</a>
</div>
</body></html>
