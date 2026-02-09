<?php
require __DIR__ . '/db.php';
session_start();
$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username']);
    $pass = $_POST['password'];
    $stmt = $conn->prepare("SELECT id, password_hash FROM admin_users WHERE username = ? LIMIT 1");
    $stmt->bind_param('s', $user);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        if (password_verify($pass, $row['password_hash'])) {
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_user'] = $user;
            header("Location: admin_dashboard.php");
            exit;
        } else {
            $err = "Invalid credentials.";
        }
    } else {
        $err = "Invalid credentials.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Admin Login</title></head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="card p-3" style="max-width:420px;">
            <h3 class="text-danger">Admin Login</h3>
            <?php if ($err) echo "<div class='alert alert-danger'>$err</div>"; ?>
            <form method="post">
                <input class="form-control mb-2" name="username" placeholder="Username" required>
                <input class="form-control mb-2" type="password" name="password" placeholder="Password" required>
                <button class="btn btn-custom">Login</button>
            </form>
        </div>
    </div>
</body>
</html>
