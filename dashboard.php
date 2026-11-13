<?php
require_once "config.php";
requireLogin();

if ($_SESSION["role"] !== "student") {
    header("Location: admin/index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student Dashboard | ScholarConnect</title>

    <link rel="stylesheet" href="style.css">

</head>

<body class="dashboard-body">

    <div class="dashboard-container">

        <header class="dashboard-header">

            <div class="brand">

                <div class="brand-icon">S</div>

                <div>
                    <h2>ScholarConnect</h2>
                    <span>SCHOLARSHIP MANAGEMENT SYSTEM</span>
                </div>

            </div>

            <a href="logout.php" class="logout-button">
                Sign Out
            </a>

        </header>

        <main class="dashboard-content">

            <div class="welcome-label">
                STUDENT DASHBOARD
            </div>

            <h1>
                Welcome,
                <?= e($_SESSION["student_name"]) ?>!
            </h1>

            <p>
                Welcome to your ScholarConnect student account.
                You can manage your scholarship activities here.
            </p>

            <div class="student-info-card">

                <h2>Student Information</h2>

                <div class="info-row">
                    <span>Student ID</span>
                    <b><?= e($_SESSION["student_number"]) ?></b>
                </div>

                <div class="info-row">
                    <span>Full Name</span>
                    <b><?= e($_SESSION["student_name"]) ?></b>
                </div>

                <div class="info-row">
                    <span>Email Address</span>
                    <b><?= e($_SESSION["student_email"]) ?></b>
                </div>

                <div class="info-row">
                    <span>Account Type</span>
                    <b>Student</b>
                </div>

            </div>

            <div class="coming-soon-card">

                <h2>Scholarship Management</h2>

                <p>
                    Your student account is ready.
                    Scholarship listings and application management
                    can be added to this dashboard next.
                </p>

            </div>

        </main>

    </div>

</body>

</html>