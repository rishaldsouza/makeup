<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: ../login.php"); exit; }
include '../../config/db.php';
include '../includes/header.php';

$res = mysqli_query($conn,"
    SELECT o.id, o.total, o.payment_status, o.payment_method, o.created_at,
           o.recipient_name, o.phone, o.delivery_address,
           u.name AS user_name, u.email AS user_email
    FROM orders o LEFT JOIN users u ON o.user_id=u.id
    ORDER BY o.created_at DESC
");
?>
<?php include '../includes/sidebar.php'; ?>
<div class="main">
    <h1>Orders</h1>
    <div class="page-card">
        <table>
            <thead><tr><th>#</th><th>Customer</th><th>Deliver To</th><th>Amount</th><th>Method</th><th>Status</th><th>Date</th></tr></thead>
            <tbody>
            <?php if($res && mysqli_num_rows($res)>0): while($o=mysqli_fetch_assoc($res)): ?>
                <?php
                $st  = strtolower($o['payment_status'] ?? '');
                $cls = (str_contains($st,'cod')||str_contains($st,'delivery')) ? 'cod' : (str_contains($st,'paid') ? 'paid' : 'pending');
                $recipientName = $o['recipient_name'] ?? $o['user_name'] ?? 'Unknown';
                ?>
                <tr>
                    <td>#<?=htmlspecialchars($o['id'])?></td>
                    <td>
                        <strong><?=htmlspecialchars($o['user_name']??'Unknown')?></strong><br>
                        <span style="color:var(--muted);font-size:12px"><?=htmlspecialchars($o['user_email']??'')?></span>
                    </td>
                    <td style="max-width:220px">
                        <?php if(!empty($o['recipient_name'])): ?>
                            <strong style="font-size:13px"><?=htmlspecialchars($o['recipient_name'])?></strong><br>
                            <?php if(!empty($o['phone'])): ?><span style="color:var(--muted);font-size:12px">📞 <?=htmlspecialchars($o['phone'])?></span><br><?php endif; ?>
                            <?php if(!empty($o['delivery_address'])): ?><span style="color:var(--muted);font-size:12px;line-height:1.5"><?=htmlspecialchars($o['delivery_address'])?></span><?php endif; ?>
                        <?php else: ?>
                            <span style="color:#bbb;font-size:12px">—</span>
                        <?php endif; ?>
                    </td>
                    <td style="font-weight:700;color:var(--rose)">₹<?=number_format($o['total'],2)?></td>
                    <td><?=htmlspecialchars($o['payment_method']??'COD')?></td>
                    <td><span class="badge <?=$cls?>"><?=htmlspecialchars($o['payment_status'])?></span></td>
                    <td style="color:var(--muted);font-size:13px"><?=date('d M Y, h:i A',strtotime($o['created_at']))?></td>
                </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="7" style="text-align:center;color:var(--muted);padding:40px">No orders yet</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
