<?php
require_once "../config.php";
requireAdmin();
?>

<!DOCTYPE html>
<html>

<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="../style.css">
</head>

<body>

    <div class="container">
        <h1>Admin Dashboard</h1>

        <h2>Welcome,
            <?= e($_SESSION["full_name"]) ?>!
        </h2>

        <p>You are logged in as an administrator.</p>

        <a href="users.php">Manage Users</a>
        <br><br>
        <a href="../logout.php">Sign Out</a>
    </div>

</body>

</html>