<?php
if (!isset($_SESSION['admin'])) {
    header("Location: ../login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Beauty Canvas Admin</title>
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{--rose:#e8547a;--rose-dark:#c93560;--sidebar:#1a1a2e;--text:#1a1a2e;--muted:#7b7b8e;--bg:#f8f9fc}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:'DM Sans',sans-serif;background:var(--bg);color:var(--text)}
.admin-wrapper{display:flex;min-height:100vh}
.sidebar{width:240px;background:var(--sidebar);color:#fff;padding:28px 20px;flex-shrink:0}
.sidebar .brand{font-size:18px;font-weight:700;color:#fff;margin-bottom:4px}
.sidebar .brand span{color:var(--rose)}
.sidebar .brand-sub{font-size:11px;color:#666;margin-bottom:32px}
.sidebar a{display:flex;align-items:center;gap:10px;color:#aaa;text-decoration:none;padding:10px 12px;border-radius:10px;font-size:14px;font-weight:500;margin-bottom:4px;transition:all .2s}
.sidebar a:hover,.sidebar a.active{background:rgba(232,84,122,.15);color:#fff}
.sidebar a .icon{font-size:16px}
.sidebar .sep{border:none;border-top:1px solid #333;margin:16px 0}
.main{flex:1;padding:32px;overflow-x:auto}
.main h1{font-size:26px;font-weight:700;color:var(--text);margin-bottom:24px}
.cards{display:grid;grid-template-columns:repeat(3,1fr);gap:20px;margin-bottom:32px}
.stat-card{background:#fff;border-radius:16px;padding:24px;box-shadow:0 2px 12px rgba(0,0,0,.06)}
.stat-card h4{font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:12px}
.stat-card .number{font-size:38px;font-weight:700;color:var(--text)}
.stat-card .icon{font-size:28px;margin-bottom:8px}
.page-card{background:#fff;border-radius:16px;padding:28px;box-shadow:0 2px 12px rgba(0,0,0,.06)}
.page-card h2{font-size:20px;font-weight:600;margin-bottom:20px}
table{width:100%;border-collapse:collapse}
th,td{padding:12px 16px;text-align:left;border-bottom:1px solid #f0f0f5;font-size:14px}
th{font-size:12px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;background:#f8f9fc}
tr:hover td{background:#fefefe}
.badge{display:inline-flex;padding:4px 12px;border-radius:20px;font-size:12px;font-weight:600}
.badge.paid,.badge.active{background:#e8f5e9;color:#2e7d32}
.badge.cod{background:#e3f2fd;color:#1565c0}
.badge.pending{background:#fff8e1;color:#f57f17}
.badge.inactive{background:#ffebee;color:#c62828}
.btn{display:inline-flex;align-items:center;gap:6px;padding:8px 14px;border-radius:8px;text-decoration:none;font-size:13px;font-weight:600;transition:all .2s;border:none;cursor:pointer}
.btn-primary{background:var(--rose);color:white}.btn-primary:hover{background:var(--rose-dark)}
.btn-edit{background:#e3f2fd;color:#1565c0}.btn-edit:hover{background:#bbdefb}
.btn-delete{background:#ffebee;color:#c62828}.btn-delete:hover{background:#ffcdd2}
.btn-success{background:#e8f5e9;color:#2e7d32}
.form-group{margin-bottom:18px}
.form-group label{display:block;font-size:13px;font-weight:600;color:var(--muted);margin-bottom:6px}
.form-group input,.form-group select,.form-group textarea{width:100%;padding:12px 14px;border:1.5px solid #e0e0e0;border-radius:10px;font-size:14px;font-family:inherit;outline:none;transition:border .2s}
.form-group input:focus,.form-group select:focus,.form-group textarea:focus{border-color:var(--rose)}
</style>
</head>
<body>
<div class="admin-wrapper">
