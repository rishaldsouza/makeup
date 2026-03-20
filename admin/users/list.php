<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: ../login.php"); exit; }
include '../../config/db.php';
include '../includes/header.php';

// Handle delete
if (isset($_GET['delete_id'])) {
    $did = (int)$_GET['delete_id'];
    mysqli_query($conn,"DELETE FROM users WHERE id=$did");
    header("Location: list.php"); exit;
}

$res = mysqli_query($conn,"SELECT * FROM users ORDER BY created_at DESC");
?>
<?php include '../includes/sidebar.php'; ?>
<div class="main">
    <h1>Users</h1>
    <div class="page-card">
        <table>
            <thead><tr><th>#</th><th>Name</th><th>Email</th><th>Status</th><th>Registered</th><th>Actions</th></tr></thead>
            <tbody>
            <?php if(mysqli_num_rows($res)>0): $i=1; while($u=mysqli_fetch_assoc($res)): ?>
                <tr>
                    <td><?=$i++?></td>
                    <td style="font-weight:600"><?=htmlspecialchars($u['name'])?></td>
                    <td><?=htmlspecialchars($u['email'])?></td>
                    <td><span class="badge <?=($u['status']??'Active')==='Active'?'active':'inactive'?>"><?=htmlspecialchars($u['status']??'Active')?></span></td>
                    <td style="color:var(--muted);font-size:13px"><?=isset($u['created_at'])?date('d M Y',strtotime($u['created_at'])):'-'?></td>
                    <td><a href="list.php?delete_id=<?=$u['id']?>" class="btn btn-delete" onclick="return confirm('Delete user?')">Delete</a></td>
                </tr>
            <?php endwhile; else: ?>
                <tr><td colspan="6" style="text-align:center;color:var(--muted);padding:40px">No users yet</td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?php include '../includes/footer.php'; ?>
