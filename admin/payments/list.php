<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: ../login.php"); exit; }
include '../../config/db.php';
include '../includes/header.php';

$res = mysqli_query($conn,"
    SELECT o.id, o.total, o.payment_status, o.payment_method, o.created_at,
           u.name AS user_name
    FROM orders o LEFT JOIN users u ON o.user_id=u.id
    ORDER BY o.created_at DESC
");
?>
<?php include '../includes/sidebar.php'; ?>
<div class="main">
    <h1>Payments</h1>
    <div class="page-card">
        <table>
            <thead><tr><th>Order #</th><th>Customer</th><th>Amount</th><th>Method</th><th>Status</th><th>Date</th></tr></thead>
            <tbody>
            <?php if(mysqli_num_rows($res)>0): while($p=mysqli_fetch_assoc($res)): ?>
                <?php
                $st = strtolower($p['payment_status']);
                $cls = str_contains($st,'cod')||str_contains($st,'delivery') ? 'cod' : (str_contains($st,'paid') ? 'paid' : 'pending');
                ?>
                <tr>
                    <td>#<?=htmlspecialchars($p['id'])?></td>
                    <td><?=htmlspecialchars($p['user_name']??'Unknown')?></td>
                    <td style="font-weight:700;color:var(--rose)">₹<?=number_format($p['total'],2)?></td>
                    <td><?=htmlspecialchars($p['payment_method']??'COD')?></td>
                    <td><span class="badge <?=$cls?>"><?=htmlspecialchars($p['payment_status'])?></span></td>
                    <td style="color:var(--muted);font-size:13px"><?=date('d M Y, h:i A',strtotime($p['created_at']))?></td>
                </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="6" style="text-align:center;color:var(--muted);padding:40px">No payments yet</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
