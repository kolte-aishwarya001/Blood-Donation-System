<?php
session_start();
require __DIR__ . '/db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format!";
    } else {
        $stmt = $conn->prepare("SELECT * FROM donors WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows === 1) {
            $donor = $res->fetch_assoc();
            if (password_verify($password, $donor['password'])) {
                $_SESSION['donor_email'] = $donor['email'];
                header("Location: index.php");
                exit;
            } else {
                $error = "Incorrect password!";
            }
        } else {
            $error = "No donor found with this email!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Donor Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color:#f9f9f9; display:flex; justify-content:center; align-items:center; height:100vh;}
        .login-box { background:white; padding:30px; border-radius:15px; box-shadow:0 0 15px rgba(0,0,0,0.1); width:400px;}
        .btn-custom { background-color:#b10000; color:white; width:100%; }
        .btn-custom:hover { background-color:#a00000; }
        .error { color:red; font-weight:bold; margin-top:10px;}
    </style>
</head>
<body>
    <form method="post" class="login-box">
        <h3 class="text-center mb-3 text-danger">🔑 Donor Login</h3>
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required placeholder="Enter your email">
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required
                   pattern="(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}"
                   title="Must contain at least 8 characters, 1 uppercase, 1 number, and 1 special character.">
        </div>
        <button type="submit" class="btn btn-custom">Login</button>
        <?php if ($error): ?>
            <div class="error text-center"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
    </form>
</body>
</html>
