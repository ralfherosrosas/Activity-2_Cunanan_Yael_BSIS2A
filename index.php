<?php

require_once "config.php";

if (isset($_SESSION["student_id"])) {
    header("Location: dashboard.php");
    exit;
}

$error = $_SESSION["error"] ?? "";
unset($_SESSION["error"]);

$success = $_SESSION["success"] ?? "";
unset($_SESSION["success"]);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Sign In | ScholarConnect</title>

    <link rel="stylesheet" href="style.css">

    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap"
        rel="stylesheet">

</head>

<body>

    <div class="auth-container">

        <!-- LEFT SIDE -->

        <div class="auth-left">

            <div class="brand">

                <div class="brand-icon">S</div>

                <div>
                    <h2>ScholarConnect</h2>
                    <span>SCHOLARSHIP MANAGEMENT SYSTEM</span>
                </div>

            </div>

            <div class="left-content">

                <div class="welcome-label">
                    YOUR FUTURE STARTS HERE
                </div>

                <h1>
                    Education is the key to a brighter future.
                </h1>

                <p>
                    Discover scholarship opportunities, manage your applications,
                    and take the next step toward achieving your academic dreams.
                </p>

                <div class="feature-list">

                    <div class="feature">
                        <span>✓</span>
                        <div>
                            <b>Find Opportunities</b>
                            <small>Explore available scholarship programs.</small>
                        </div>
                    </div>

                    <div class="feature">
                        <span>✓</span>
                        <div>
                            <b>Manage Applications</b>
                            <small>Keep your scholarship applications organized.</small>
                        </div>
                    </div>

                    <div class="feature">
                        <span>✓</span>
                        <div>
                            <b>Track Your Progress</b>
                            <small>Stay updated on your application status.</small>
                        </div>
                    </div>

                </div>

            </div>

            <div class="left-footer">
                © 2026 ScholarConnect. All rights reserved.
            </div>

        </div>


        <!-- RIGHT SIDE -->

        <div class="auth-right">

            <div class="auth-card">

                <div class="mobile-brand">
                    <div class="brand-icon">S</div>
                    <b>ScholarConnect</b>
                </div>

                <div class="form-heading">

                    <span class="form-label">
                        STUDENT PORTAL
                    </span>

                    <h2>Welcome Back!</h2>

                    <p>
                        Sign in to access your scholarship account.
                    </p>

                </div>


                <?php if ($error): ?>

                    <div class="message error">
                        <?= e($error) ?>
                    </div>

                <?php endif; ?>


                <?php if ($success): ?>

                    <div class="message success">
                        <?= e($success) ?>
                    </div>

                <?php endif; ?>


                <form action="login.php" method="POST">

                    <input type="hidden" name="csrf_token" value="<?= e($_SESSION["csrf_token"]) ?>">

                    <div class="form-group">

                        <label>Email Address</label>

                        <input type="email" name="email" placeholder="Enter your email address" autocomplete="email"
                            required>

                    </div>


                    <div class="form-group">

                        <label>Password</label>

                        <div class="password-field">

                            <input type="password" name="password" id="loginPassword" placeholder="Enter your password"
                                autocomplete="current-password" required>

                            <button type="button" class="show-password" onclick="togglePassword('loginPassword', this)">
                                Show
                            </button>

                        </div>

                    </div>


                    <button type="submit" class="submit-button">
                        Sign In
                        <span>→</span>
                    </button>

                </form>


                <div class="form-divider">
                    <span>NEW TO SCHOLARCONNECT?</span>
                </div>


                <a href="register.php" class="register-button">
                    Create Student Account
                </a>


                <p class="terms-text">
                    By signing in, you agree to our
                    <a href="#">Terms of Service</a>
                    and
                    <a href="#">Privacy Policy</a>.
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