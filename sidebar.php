<?php
// Sidebar links
$links = [
    'Home' => 'index.php',
    'Register Donor' => 'register.php',
    'View Donors' => 'view_donors.php',
    'Admin Login' => 'admin_login.php',
    'Donor Login' => 'donor_login.php'
];
?>
<style>
    body {
        margin: 0;
        font-family: 'Segoe UI', sans-serif;
    }

    .sidebar {
        position: fixed;
        left: 0;
        top: 0;
        width: 240px;
        height: 100vh;
        background-color: #b10000; /* Sidebar background */
        color: white;
        display: flex;
        flex-direction: column;
        padding-top: 20px;
        box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        z-index: 1000;
    }

    /* Sidebar title styling - now white */
    .sidebar h2 {
        text-align: center;
        margin-bottom: 20px;
        font-size: 22px;
        color: white; /* Changed to white */
        text-shadow: 1px 1px 2px rgba(0,0,0,0.3);
    }

    .sidebar a {
        color: white;
        text-decoration: none;
        padding: 12px 20px;
        margin: 5px 0;
        display: block;
        border-radius: 5px;
        transition: 0.2s;
    }

    .sidebar a:hover {
        background-color: #d60000;
    }

    .main-content {
        margin-left: 240px; /* leave space for sidebar */
        padding: 20px;
    }
</style>

<div class="sidebar">
    <h2>🩸 Blood Donation</h2>
    <?php foreach ($links as $name => $url): ?>
        <a href="<?= $url ?>"><?= $name ?></a>
    <?php endforeach; ?>
</div>
