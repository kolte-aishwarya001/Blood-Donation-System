<?php
require __DIR__ . '/db.php';

// Blood groups list
$groups = ['A+','A-','B+','B-','O+','O-','AB+','AB-'];

// Fetch counts by blood group
$blood_counts = [];
$res = $conn->query("SELECT blood_group, COUNT(*) AS total FROM donors GROUP BY blood_group");
if ($res) {
    while ($r = $res->fetch_assoc()) {
        $blood_counts[$r['blood_group']] = (int)$r['total'];
    }
}

// Fetch all donors
$donorsRes = $conn->query("SELECT * FROM donors ORDER BY blood_group, name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Blood Donation System</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #fff5f5;
            font-family: Arial, sans-serif;
        }
        .main-content {
            margin-left: 230px; /* same width as sidebar */
            padding: 20px;
        }
        .card {
            margin-bottom: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .welcome-box img {
            width: 80px;
            margin-bottom: 10px;
        }
        .btn-custom {
            background-color: #b10000;
            color: white;
            margin: 5px;
        }
        .btn-custom:hover {
            background-color: #7a0000;
        }
        .donor-card {
            background-color: #fff0f0;
            padding: 10px 15px;
            border-radius: 10px;
            margin: 8px 0;
        }
        .text-danger-strong {
            color: #b10000;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <!-- Welcome Box -->
        <div class="welcome-box card text-center p-4">
           <img src="blood.png" alt="Blood Drop" style="width:80px; margin-bottom:10px;">
            <h1 style="color:#b10000;">Welcome to Blood Donation System</h1>
            <p>A platform to connect donors and patients in need.</p>
            <a href="register.php" class="btn btn-custom">Register as Donor</a>
            <a href="view_donors.php" class="btn btn-custom">View Donors</a>
        </div>

        <!-- Alerts Section -->
        <div class="alerts card p-3">
            <h3 class="text-danger">🩸 Blood Availability Alerts</h3>
            <?php foreach ($groups as $g): 
                $c = isset($blood_counts[$g]) ? $blood_counts[$g] : 0;
                if ($c < 3): ?>
                    <p class="text-danger">⚠️ Low Stock: Blood group <strong><?php echo $g; ?></strong> has only <?php echo $c; ?> donor(s).</p>
                <?php else: ?>
                    <p class="text-success">✅ <?php echo $g; ?> — <?php echo $c; ?> donor(s) available.</p>
                <?php endif;
            endforeach; ?>
        </div>

        <!-- Donors Section -->
        <div class="donor-section card p-3">
            <h3>👥 Donors Classified by Blood Group</h3>
            <?php
            foreach ($groups as $g) {
                echo "<h4 class='mt-4 text-danger-strong'>Blood Group: $g</h4>";
                $found = false;

                if ($donorsRes && $donorsRes->num_rows > 0) {
                    $donorsRes->data_seek(0);
                    while ($d = $donorsRes->fetch_assoc()) {
                        if ($d['blood_group'] === $g) {
                            $found = true;
                            echo "<div class='donor-card'>";
                            echo "<h5>".htmlspecialchars($d['name'])."</h5>";
                            echo "<p>Age: ".(int)$d['age']." | City: ".htmlspecialchars($d['city'])."</p>";
                            echo "<p>Contact: ".htmlspecialchars($d['contact'])." | Email: ".htmlspecialchars($d['email'])."</p>";
                            echo "<p>Status: <strong>".$d['availability']."</strong></p>";
                            echo "</div>";
                        }
                    }
                }
                if (!$found) echo "<p>No donors available for this group.</p>";
            }
            ?>
        </div>
    </div>
</body>
</html>
