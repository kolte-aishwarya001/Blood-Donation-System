<?php
require __DIR__ . '/db.php';
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// handle availability update or deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_availability'])) {
        $id = (int)$_POST['donor_id'];
        $avail = $_POST['availability'];
        $stmt = $conn->prepare("UPDATE donors SET availability = ? WHERE id = ?");
        $stmt->bind_param('si', $avail, $id);
        $stmt->execute();
        $stmt->close();
    } elseif (isset($_POST['delete_donor'])) {
        $id = (int)$_POST['donor_id'];
        // If deleting and donor was available, reduce stock
        $stmt = $conn->prepare("SELECT blood_group, availability FROM donors WHERE id = ? LIMIT 1");
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $res = $stmt->get_result()->fetch_assoc();
        if ($res && $res['availability'] === 'Available') {
            $bg = $res['blood_group'];
            $conn->query("UPDATE blood_stock SET available_units = GREATEST(available_units - 1,0) WHERE blood_group = '".$conn->real_escape_string($bg)."'");
        }
        $stmt->close();
        $stmt2 = $conn->prepare("DELETE FROM donors WHERE id = ?");
        $stmt2->bind_param('i', $id);
        $stmt2->execute();
        $stmt2->close();
    }
}

// fetch donors
$res = $conn->query("SELECT * FROM donors ORDER BY blood_group, name");
?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Manage Donors</title><link href="style.css" rel="stylesheet"></head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <h2>Manage Donors</h2>
        <table class="table">
            <thead><tr><th>ID</th><th>Name</th><th>Blood Group</th><th>Availability</th><th>Actions</th></tr></thead>
            <tbody>
            <?php while ($d = $res->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $d['id'];?></td>
                    <td><?php echo htmlspecialchars($d['name']);?></td>
                    <td><?php echo htmlspecialchars($d['blood_group']);?></td>
                    <td><?php echo htmlspecialchars($d['availability']);?></td>
                    <td>
                        <form method="post" style="display:inline-block">
                            <input type="hidden" name="donor_id" value="<?php echo $d['id'];?>">
                            <select name="availability" class="form-select form-select-sm" style="width:auto; display:inline-block">
                                <option value="Available">Available</option>
                                <option value="Unavailable">Unavailable</option>
                            </select>
                            <button name="update_availability" class="btn btn-sm btn-primary">Update</button>
                        </form>
                        <form method="post" style="display:inline-block" onsubmit="return confirm('Delete donor?');">
                            <input type="hidden" name="donor_id" value="<?php echo $d['id'];?>">
                            <button name="delete_donor" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
