<?php
// simple auth.php for user signup/login (optional)
require __DIR__ . '/db.php';
session_start();
$mode = $_GET['mode'] ?? 'login';
$flash = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($mode === 'register') {
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);
        $pass = $_POST['password'];
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (name,email,password_hash) VALUES (?, ?, ?)");
        $stmt->bind_param('sss', $name, $email, $hash);
        if ($stmt->execute()) {
            $flash = "Registration successful. Please login.";
            $mode = 'login';
        } else {
            $flash = "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        // login
        $email = trim($_POST['email']);
        $pass = $_POST['password'];
        $stmt = $conn->prepare("SELECT id, name, password_hash FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $r = $stmt->get_result()->fetch_assoc();
        if ($r && password_verify($pass, $r['password_hash'])) {
            $_SESSION['user_id'] = $r['id'];
            $_SESSION['user_name'] = $r['name'];
            header("Location: index.php");
            exit;
        } else {
            $flash = "Invalid credentials.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"><title>Auth</title></head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <div class="card p-3">
            <h3><?php echo $mode === 'register' ? 'Register' : 'Login'; ?></h3>
            <?php if ($flash) echo "<div class='alert alert-info'>$flash</div>"; ?>
            <form method="post">
                <?php if ($mode === 'register'): ?>
                    <input class="form-control mb-2" name="name" required placeholder="Full name">
                <?php endif; ?>
                <input class="form-control mb-2" name="email" type="email" required placeholder="Email">
                <input class="form-control mb-2" name="password" type="password" required placeholder="Password">
                <button class="btn btn-custom"><?php echo $mode === 'register' ? 'Register' : 'Login'; ?></button>
            </form>
            <p class="mt-2">
                <?php if ($mode === 'register'): ?>
                    Already have an account? <a href="auth.php?mode=login">Login</a>
                <?php else: ?>
                    No account? <a href="auth.php?mode=register">Register</a>
                <?php endif; ?>
            </p>
        </div>
    </div>
</body>
</html>
