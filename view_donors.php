<?php
require __DIR__ . '/db.php';

// Fetch all donors
$donorsRes = $conn->query("SELECT * FROM donors ORDER BY blood_group, name");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Donors</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff5f5;
        }
        .main-content {
            margin-left: 230px;
            padding: 20px;
        }
        .card {
            border-radius: 15px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 20px;
            background-color: #fff;
        }
        table {
            width: 100%;
        }
        th, td {
            text-align: center;
            padding: 10px;
        }
        th {
            background-color: #b10000;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #fff0f0;
        }
        tr:hover {
            background-color: #f8d7da;
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="main-content">
        <div class="card">
            <h2 class="text-center text-danger">👥 Donors List</h2>
            <?php if ($donorsRes && $donorsRes->num_rows > 0): ?>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Age</th>
                            <th>City</th>
                            <th>Blood Group</th>
                            <th>Availability</th>
                            <th>Contact</th>
                            <th>Email</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($d = $donorsRes->fetch_assoc()): ?>
                        <tr>
                            <td><?= htmlspecialchars($d['name']); ?></td>
                            <td><?= (int)$d['age']; ?></td>
                            <td><?= htmlspecialchars($d['city']); ?></td>
                            <td><?= $d['blood_group']; ?></td>
                            <td><?= $d['availability']; ?></td>
                            <td><?= htmlspecialchars($d['contact']); ?></td>
                            <td><?= htmlspecialchars($d['email']); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center text-danger">No donors available.</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
