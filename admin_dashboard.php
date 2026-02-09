<?php
require __DIR__ . '/db.php';
session_start();
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit;
}

// Fetch stock for dashboard
$stocks = [];
$r = $conn->query("SELECT b.blood_group, b.available_units, IFNULL(d.c,0) AS donors_count
                   FROM blood_stock b
                   LEFT JOIN (
                     SELECT blood_group, COUNT(*) c FROM donors GROUP BY blood_group
                   ) d ON d.blood_group = b.blood_group
                   ORDER BY b.blood_group");
while ($row = $r->fetch_assoc()) $stocks[] = $row;

// Low stock threshold
$threshold = 3;
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin Dashboard</title>
    <link href="style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <div class="main-content">
        <h2>Admin Dashboard</h2>
        <div class="card p-3 mb-3">
            <h4>Low Stock Alerts</h4>
            <?php foreach ($stocks as $s): 
                $g = htmlspecialchars($s['blood_group']);
                $u = (int)$s['available_units'];
                if ($u < $threshold) {
                    echo "<p class='text-danger'>⚠️ $g: only $u unit(s) available.</p>";
                } else {
                    echo "<p class='text-success'>✅ $g: $u unit(s) available.</p>";
                }
            endforeach; ?>
        </div>

        <div class="card p-3 mb-3">
            <h4>Blood Stock Table</h4>
            <table class="table">
                <thead><tr><th>Blood Group</th><th>Available Units</th></tr></thead>
                <tbody>
                <?php foreach ($stocks as $s): ?>
                    <tr><td><?php echo htmlspecialchars($s['blood_group']);?></td><td><?php echo (int)$s['available_units'];?></td></tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="card p-3">
            <h4>Visual: Donors by Blood Group</h4>
            <canvas id="byGroup" width="400" height="150"></canvas>
        </div>
    </div>

    <script>
    const labels = <?php echo json_encode(array_column($stocks,'blood_group'));?>;
    const dataVals = <?php echo json_encode(array_map('intval', array_column($stocks,'available_units')));?>;
    new Chart(document.getElementById('byGroup'), {
        type: 'bar',
        data: { labels: labels, datasets: [{ label: 'Available Units', data: dataVals }] },
        options: { scales: { y: { beginAtZero: true } } }
    });
    </script>
</body>
</html>
