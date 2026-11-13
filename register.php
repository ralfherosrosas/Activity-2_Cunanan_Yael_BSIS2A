<?php

require_once "config.php";

if (isset($_SESSION["student_id"])) {
    header("Location: dashboard.php");
    exit;
}

$error = $_SESSION["error"] ?? "";
unset($_SESSION["error"]);

$studentId = "";
$fullName = "";
$email = "";

/* PROCESS REGISTRATION */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $token = $_POST["csrf_token"] ?? "";

    if (
        !is_string($token) ||
        !hash_equals($_SESSION["csrf_token"], $token)
    ) {
        $_SESSION["error"] = "Invalid session. Please try again.";
        header("Location: register.php");
        exit;
    }

    $studentId = trim($_POST["student_id"] ?? "");
    $fullName = trim($_POST["full_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirmPassword = $_POST["confirm_password"] ?? "";

    if (
        $studentId === "" ||
        $fullName === "" ||
        $email === "" ||
        $password === "" ||
        $confirmPassword === ""
    ) {
        $error = "Please complete all fields.";
    } elseif (
        strlen($studentId) > 50 ||
        strlen($fullName) > 150 ||
        strlen($email) > 150
    ) {
        $error = "One or more fields are too long.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (strlen($password) < 8) {
        $error = "Password must contain at least 8 characters.";
    } elseif ($password !== $confirmPassword) {
        $error = "Passwords do not match.";
    } else {

        try {

            $hashedPassword = password_hash(
                $password,
                PASSWORD_DEFAULT
            );

            $stmt = $conn->prepare(
                "INSERT INTO students
                (student_id, full_name, email, password)
                VALUES (?, ?, ?, ?)"
            );

            $stmt->bind_param(
                "ssss",
                $studentId,
                $fullName,
                $email,
                $hashedPassword
            );

            $stmt->execute();

            $_SESSION["success"] =
                "Registration successful! You can now sign in.";

            header("Location: index.php");
            exit;

        } catch (mysqli_sql_exception $e) {

            if ((int) $e->getCode() === 1062) {
                $error = "Student ID or email is already registered.";
            } else {
                error_log($e->getMessage());
                $error = "Registration failed. Please try again.";
            }
            $stmt = $conn->prepare(
                "INSERT INTO students
    (student_id, full_name, email, password)
    VALUES (?, ?, ?, ?)"
            );

        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Register | ScholarConnect</title>

    <link rel="stylesheet" href="style.css">

    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

</head>

<body>

    <div class="auth-container">

        <div class="auth-left">

            <div class="brand">

                <div class="brand-icon">S</div>

                <div>
                    <h2>ScholarConnect</h2>
                    <span>SCHOLARSHIP MANAGEMENT SYSTEM</span>
                </div>

            </div>

            <div class="left-content">

                <div class="welcome-label">BUILD YOUR FUTURE</div>

                <h1>
                    Every opportunity begins with a single step.
                </h1>

                <p>
                    Create your student account and start exploring
                    scholarship opportunities that can support your education.
                </p>

                <div class="feature-list">

                    <div class="feature">
                        <span>✓</span>
                        <div>
                            <b>Easy Registration</b>
                            <small>Create your student account in minutes.</small>
                        </div>
                    </div>

                    <div class="feature">
                        <span>✓</span>
                        <div>
                            <b>Scholarship Access</b>
                            <small>Discover available scholarship programs.</small>
                        </div>
                    </div>

                    <div class="feature">
                        <span>✓</span>
                        <div>
                            <b>Student Dashboard</b>
                            <small>Manage your scholarship activities.</small>
                        </div>
                    </div>

                </div>

            </div>

            <div class="left-footer">
                © 2026 ScholarConnect. All rights reserved.
            </div>

        </div>

        <div class="auth-right">

            <div class="auth-card register-card">

                <div class="mobile-brand">
                    <div class="brand-icon">S</div>
                    <b>ScholarConnect</b>
                </div>

                <div class="form-heading">

                    <span class="form-label">STUDENT REGISTRATION</span>

                    <h2>Create Account</h2>

                    <p>
                        Register now to access scholarship opportunities.
                    </p>

                </div>

                <?php if ($error): ?>
                    <div class="message error">
                        <?= e($error) ?>
                    </div>
                <?php endif; ?>

                <form action="register.php" method="POST">

                    <input type="hidden" name="csrf_token" value="<?= e($_SESSION["csrf_token"]) ?>">

                    <div class="form-group">

                        <label>Student ID</label>

                        <input type="text" name="student_id" placeholder="Enter your student ID" maxlength="50"
                            value="<?= e($studentId) ?>" required>

                    </div>

                    <div class="form-group">

                        <label>Full Name</label>

                        <input type="text" name="full_name" placeholder="Enter your complete name" maxlength="150"
                            value="<?= e($fullName) ?>" required>

                    </div>

                    <div class="form-group">

                        <label>Email Address</label>

                        <input type="email" name="email" placeholder="Enter your email address" maxlength="150"
                            value="<?= e($email) ?>" required>

                    </div>

                    <div class="form-group">

                        <label>Password</label>

                        <div class="password-field">

                            <input type="password" name="password" id="registerPassword" placeholder="Create a password"
                                minlength="8" required>

                            <button type="button" class="show-password"
                                onclick="togglePassword('registerPassword', this)">
                                Show
                            </button>

                        </div>

                        <small class="input-hint">
                            Password must contain at least 8 characters.
                        </small>

                    </div>

                    <div class="form-group">

                        <label>Confirm Password</label>

                        <div class="password-field">

                            <input type="password" name="confirm_password" id="confirmPassword"
                                placeholder="Confirm your password" minlength="8" required>

                            <button type="button" class="show-password"
                                onclick="togglePassword('confirmPassword', this)">
                                Show
                            </button>

                        </div>

                    </div>

                    <button type="submit" class="submit-button">
                        Create Account
                        <span>→</span>
                    </button>

                </form>

                <div class="login-link">
                    Already have an account?
                    <a href="index.php">Sign In</a>
                </div>

                <p class="terms-text">
                    Your account information will be securely stored
                    in the scholarship management database.
                </p>

            </div>

        </div>

    </div>

    <script>

        function togglePassword(id, button) {

            const input = document.getElementById(id);

            if (input.type === "password") {
                input.type = "text";
                button.textContent = "Hide";
            } else {
                input.type = "password";
                button.textContent = "Show";
            }

        }

    </script>

</body>

</html>