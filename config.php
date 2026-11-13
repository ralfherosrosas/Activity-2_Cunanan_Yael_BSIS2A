<?php
session_start();

$conn = new mysqli("localhost", "root", "", "scholarship_db");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

if (empty($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, "UTF-8");
}

function requireLogin()
{
    if (empty($_SESSION["user_id"])) {
        header("Location: index.php");
        exit();
    }
}

function requireAdmin()
{
    requireLogin();

    if (($_SESSION["role"] ?? "") !== "admin") {
        http_response_code(403);
        exit("Access denied. Admin access is required.");
    }
}

function verifyCsrf()
{
    if (
        !isset($_POST["csrf_token"]) ||
        !hash_equals($_SESSION["csrf_token"], $_POST["csrf_token"])
    ) {
        http_response_code(403);
        exit("Invalid request. Please refresh the page and try again.");
    }
}
?>