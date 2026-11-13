<?php
require_once "config.php";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verifyCsrf();

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";

    $stmt = $conn->prepare(
        "SELECT id, student_id, full_name, email, password, role, status
         FROM students WHERE email = ? LIMIT 1"
    );

    $stmt->bind_param("s", $email);
    $stmt->execute();

    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {

        if ($user["status"] !== "active") {
            $_SESSION["error"] = "Your account is inactive. Please contact the administrator.";
            header("Location: index.php");
            exit();
        }

        if (password_verify($password, $user["password"])) {

            session_regenerate_id(true);

            $_SESSION["user_id"] = $user["id"];
            $_SESSION["student_id"] = $user["student_id"];
            $_SESSION["full_name"] = $user["full_name"];
            $_SESSION["email"] = $user["email"];
            $_SESSION["role"] = $user["role"];

            if ($user["role"] === "admin") {
                header("Location: admin/index.php");
            } else {
                header("Location: dashboard.php");
            }

            exit();

        }
    }

    $_SESSION["error"] = "Invalid email or password.";
    header("Location: index.php");
    exit();
}
?>