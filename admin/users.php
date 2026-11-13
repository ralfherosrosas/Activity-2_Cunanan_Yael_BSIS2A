<?php
require_once "../config.php";
requireAdmin();

$message = "";
$error = "";

// CREATE, UPDATE, DELETE
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verifyCsrf();

    $action = $_POST["action"] ?? "";

    if ($action === "add" || $action === "update") {

        $id = (int) ($_POST["id"] ?? 0);
        $student_id = trim($_POST["student_id"] ?? "");
        $full_name = trim($_POST["full_name"] ?? "");
        $email = trim($_POST["email"] ?? "");
        $password = $_POST["password"] ?? "";
        $role = $_POST["role"] ?? "student";
        $status = $_POST["status"] ?? "active";

        if (
            $student_id === "" ||
            $full_name === "" ||
            !filter_var($email, FILTER_VALIDATE_EMAIL) ||
            !in_array($role, ["student", "admin"], true) ||
            !in_array($status, ["active", "inactive"], true)
        ) {
            $error = "Please enter valid account information.";
        } elseif ($action === "add" && $password === "") {
            $error = "Password is required for a new account.";
        } elseif ($password !== "" && strlen($password) < 8) {
            $error = "Password must contain at least 8 characters.";
        } else {

            if ($action === "add") {

                $hashedPassword = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

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

            } else {

                // Prevent an administrator from removing their own admin access.
                if (
                    $id === (int) $_SESSION["user_id"] &&
                    ($role !== "admin" || $status !== "active")
                ) {
                    $error = "You cannot deactivate or remove your own admin access.";
                } else {

                    if ($password !== "") {
                        $hashedPassword = password_hash(
                            $password,
                            PASSWORD_DEFAULT
                        );

                        $stmt = $conn->prepare(
                            "UPDATE students
                             SET student_id=?, full_name=?, email=?,
                                 password=?, role=?, status=?
                             WHERE id=?"
                        );

                        $stmt->bind_param(
                            "ssssssi",
                            $student_id,
                            $full_name,
                            $email,
                            $hashedPassword,
                            $role,
                            $status,
                            $id
                        );

                    } else {

                        $stmt = $conn->prepare(
                            "UPDATE students
                             SET student_id=?, full_name=?, email=?,
                                 role=?, status=?
                             WHERE id=?"
                        );

                        $stmt->bind_param(
                            "sssssi",
                            $student_id,
                            $full_name,
                            $email,
                            $role,
                            $status,
                            $id
                        );
                    }
                }
            }

            if ($error === "" && isset($stmt)) {
                if ($stmt->execute()) {
                    header("Location: users.php?success=1");
                    exit();
                } else {
                    $error = "Unable to save account. Check that the Student ID and email are not already in use.";
                }
            }
        }
    }

    if ($action === "delete") {
        $id = (int) ($_POST["id"] ?? 0);

        if ($id === (int) $_SESSION["user_id"]) {
            $error = "You cannot delete your own account.";
        } else {
            $stmt = $conn->prepare(
                "DELETE FROM students WHERE id = ?"
            );
            $stmt->bind_param("i", $id);

            if ($stmt->execute() && $stmt->affected_rows > 0) {
                header("Location: users.php?deleted=1");
                exit();
            } else {
                $error = "Unable to delete the selected account.";
            }
        }
    }
}

if (isset($_GET["success"])) {
    $message = "Account saved successfully.";
}

if (isset($_GET["deleted"])) {
    $message = "Account deleted successfully.";
}

// READ: GET ALL USERS
$result = $conn->query(
    "SELECT id, student_id, full_name, email, role, status, created_at
     FROM students ORDER BY id DESC"
);

$users = $result->fetch_all(MYSQLI_ASSOC);

// READ: GET SELECTED USER FOR EDITING
$editUser = null;

if (isset($_GET["edit"])) {
    $editId = (int) $_GET["edit"];

    $stmt = $conn->prepare(
        "SELECT id, student_id, full_name, email, role, status
         FROM students WHERE id = ?"
    );

    $stmt->bind_param("i", $editId);
    $stmt->execute();

    $editUser = $stmt->get_result()->fetch_assoc();
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>User Management</title>
    <link rel="stylesheet" href="../style.css">

    <style>
        .admin-content {
            max-width: 1200px;
            margin: 30px auto;
            padding: 20px;
        }

        .user-form {
            background: white;
            padding: 25px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .user-form input,
        .user-form select {
            width: 100%;
            padding: 10px;
            margin: 8px 0 15px;
            box-sizing: border-box;
        }

        .user-table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        .user-table th,
        .user-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .user-table th {
            background: #27374D;
            color: white;
        }

        .action-link {
            margin-right: 8px;
        }

        .message {
            color: green;
            margin-bottom: 15px;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }

        .delete-form {
            display: inline;
        }

        .delete-button {
            background: #c0392b;
            color: white;
            border: none;
            padding: 7px 10px;
            cursor: pointer;
        }
    </style>
</head>

<body>

    <div class="admin-content">

        <h1>User Management</h1>

        <p>
            <a href="index.php">Admin Dashboard</a> |
            <a href="../logout.php">Sign Out</a>
        </p>

        <?php if ($message): ?>
            <p class="message">
                <?= e($message) ?>
            </p>
        <?php endif; ?>

        <?php if ($error): ?>
            <p class="error">
                <?= e($error) ?>
            </p>
        <?php endif; ?>

        <div class="user-form">

            <h2>
                <?= $editUser ? "Edit User" : "Add New User" ?>
            </h2>

            <form method="POST">

                <input type="hidden" name="csrf_token" value="<?= e($_SESSION["csrf_token"]) ?>">

                <input type="hidden" name="action" value="<?= $editUser ? "update" : "add" ?>">

                <?php if ($editUser): ?>
                    <input type="hidden" name="id" value="<?= (int) $editUser["id"] ?>">
                <?php endif; ?>

                <label>Student ID</label>
                <input type="text" name="student_id" required value="<?= e($editUser["student_id"] ?? "") ?>">

                <label>Full Name</label>
                <input type="text" name="full_name" required value="<?= e($editUser["full_name"] ?? "") ?>">

                <label>Email</label>
                <input type="email" name="email" required value="<?= e($editUser["email"] ?? "") ?>">

                <label>
                    Password
                    <?= $editUser ? "(Leave blank to keep current password)" : "" ?>
                </label>

                <input type="password" name="password" <?= $editUser ? "" : "required" ?> minlength="8">

                <label>Account Role</label>
                <select name="role">
                    <option value="student" <?= ($editUser["role"] ?? "student") === "student" ? "selected" : "" ?>>
                        Student
                    </option>

                    <option value="admin" <?= ($editUser["role"] ?? "") === "admin" ? "selected" : "" ?>>
                        Admin
                    </option>
                </select>

                <label>Account Status</label>
                <select name="status">
                    <option value="active" <?= ($editUser["status"] ?? "active") === "active" ? "selected" : "" ?>>
                        Active
                    </option>

                    <option value="inactive" <?= ($editUser["status"] ?? "") === "inactive" ? "selected" : "" ?>>
                        Inactive
                    </option>
                </select>

                <button type="submit">
                    <?= $editUser ? "Update User" : "Add User" ?>
                </button>

                <?php if ($editUser): ?>
                    <a href="users.php">Cancel Edit</a>
                <?php endif; ?>

            </form>
        </div>

        <h2>Registered Users</h2>

        <table class="user-table">

            <tr>
                <th>ID</th>
                <th>Student ID</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Role</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>

            <?php foreach ($users as $user): ?>
                <tr>
                    <td>
                        <?= (int) $user["id"] ?>
                    </td>
                    <td>
                        <?= e($user["student_id"]) ?>
                    </td>
                    <td>
                        <?= e($user["full_name"]) ?>
                    </td>
                    <td>
                        <?= e($user["email"]) ?>
                    </td>
                    <td>
                        <?= e(ucfirst($user["role"])) ?>
                    </td>
                    <td>
                        <?= e(ucfirst($user["status"])) ?>
                    </td>

                    <td>
                        <a class="action-link" href="users.php?edit=<?= (int) $user["id"] ?>">
                            Edit
                        </a>

                        <?php if ((int) $user["id"] !== (int) $_SESSION["user_id"]): ?>

                            <form method="POST" class="delete-form"
                                onsubmit="return confirm('Are you sure you want to delete this account?');">

                                <input type="hidden" name="csrf_token" value="<?= e($_SESSION["csrf_token"]) ?>">

                                <input type="hidden" name="action" value="delete">

                                <input type="hidden" name="id" value="<?= (int) $user["id"] ?>">

                                <button type="submit" class="delete-button">
                                    Delete
                                </button>

                            </form>

                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>

        </table>

    </div>

</body>

</html>