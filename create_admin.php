<?php
require_once "config.php";

// CHANGE THESE DETAILS BEFORE RUNNING
$student_id = "ADMIN001";
$full_name = "System Administrator";
$email = "admin@scholarconnect.com";
$password = "ChangeThisPassword123!";

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
$role = "admin";
$status = "active";

$stmt = $conn->prepare(
    "INSERT INTO students
    (student_id, full_name, email, password, role, status)
    VALUES (?, ?, ?, ?, ?, ?)"
);

$stmt->bind_param(
    "ssssss",
    $student_id,
    $full_name,
    $email,
    $hashedPassword,
    $role,
    $status
);

if ($stmt->execute()) {
    echo "Admin account created successfully.";
} else {
    echo "Error: " . e($stmt->error);
}
?>