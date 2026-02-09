<?php
require __DIR__ . '/db.php';
$msg = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $age = (int)$_POST['age'];
    $city = trim($_POST['city']);
    $blood_group = $_POST['blood_group'];
    $availability = $_POST['availability'];
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $contact = trim($_POST['contact']);

    // Check duplicate email
    $check = $conn->prepare("SELECT id FROM donors WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $msg = "<div class='alert alert-danger'>⚠️ Email already registered! Please use another.</div>";
    } else {
        $stmt = $conn->prepare("INSERT INTO donors (name, age, city, blood_group, availability, email, password, contact) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sissssss", $name, $age, $city, $blood_group, $availability, $email, $password, $contact);

        if ($stmt->execute()) {
            $msg = "<div class='alert alert-success'>✅ Donor registered successfully!</div>";
        } else {
            $msg = "<div class='alert alert-danger'>❌ Error: Could not register donor.</div>";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Register Donor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
            font-family: 'Segoe UI', sans-serif;
        }
        .register-container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin-left: 240px; /* to adjust with sidebar */
        }
        .register-card {
            width: 650px;
            padding: 30px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0px 4px 12px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #b10000;
            margin-bottom: 20px;
        }
        .form-label {
            font-weight: 600;
        }
        .btn-custom {
            background-color: #b10000;
            color: white;
            width: 100%;
        }
        .btn-custom:hover {
            background-color: #d60000;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="register-container">
        <div class="register-card">
            <h2>🩸 Donor Registration</h2>
            <?= $msg ?>
            <form method="POST" novalidate>
                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>
                    <div class="col">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required
                            pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                            title="Enter a valid email (e.g. example@gmail.com)">
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label">Age</label>
                        <input type="number" name="age" class="form-control" min="18" max="60" required>
                    </div>
                    <div class="col">
                        <label class="form-label">City</label>
                        <input type="text" name="city" class="form-control" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label">Blood Group</label>
                        <select name="blood_group" class="form-select" required>
                            <option value="">Select</option>
                            <option>A+</option><option>A-</option>
                            <option>B+</option><option>B-</option>
                            <option>O+</option><option>O-</option>
                            <option>AB+</option><option>AB-</option>
                        </select>
                    </div>
                    <div class="col">
                        <label class="form-label">Availability</label>
                        <select name="availability" class="form-select" required>
                            <option>Available</option>
                            <option>Unavailable</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col">
                        <label class="form-label">Contact Number</label>
                        <input type="text" name="contact" class="form-control" required
                               pattern="[0-9]{10}" title="Enter a valid 10-digit number">
                    </div>
                    <div class="col">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required
                            pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}"
                            title="Password must be at least 8 characters long, include one uppercase letter, one lowercase letter, and one number">
                    </div>
                </div>

                <button type="submit" class="btn btn-custom mt-2">Register</button>
            </form>
        </div>
    </div>
</body>
</html>
