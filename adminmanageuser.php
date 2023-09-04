<?php
session_start();

if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

$connect = mysqli_connect("localhost", "root", "", "uppics");
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['deleteUser'])) {
    $userId = $_POST['userId'];

    $deleteQuery = "DELETE FROM users WHERE id = ?";
    $deleteStmt = mysqli_prepare($connect, $deleteQuery);
    mysqli_stmt_bind_param($deleteStmt, "i", $userId);
    mysqli_stmt_execute($deleteStmt);
}

if (isset($_POST['changeRole'])) {
    $userId = $_POST['userId'];
    $newRole = $_POST['newRole'];

    $updateQuery = "UPDATE users SET role = ? WHERE id = ?";
    $updateStmt = mysqli_prepare($connect, $updateQuery);
    mysqli_stmt_bind_param($updateStmt, "si", $newRole, $userId);
    mysqli_stmt_execute($updateStmt);
}

$query = "SELECT * FROM users";
$result = mysqli_query($connect, $query);
$users = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Users</title>
</head>
<body>
    <h1>Admin - Manage Users</h1>
    <p>Welcome, <?php echo $_SESSION['email']; ?></p>

    <h2>User List</h2>
    <ul>
        <?php foreach ($users as $user) : ?>
            <li>
                <p>Email: <?php echo $user['email']; ?></p>
                <p>Role: <?php echo $user['role']; ?></p>
                <?php if ($_SESSION['role'] === 'admin' && $user['email'] !== 'admin@gmail.com') : ?>
                <form method="POST">
                    <input type="hidden" name="userId" value="<?php echo $user['id']; ?>">
                    <select name="newRole">
                        <option value="user">User</option>
                        <option value="editor">Editor</option>
                        <option value="admin">Admin</option>
                    </select>
                    <button type="submit" name="changeRole">Change Role</button>
                </form>
                <?php endif; ?>
                <br>
                <?php if ($user['email'] !== 'admin@gmail.com') : ?>
                <form method="POST">
                    <input type="hidden" name="userId" value="<?php echo $user['id']; ?>">
                    <button type="submit" name="deleteUser">Delete</button>
                </form>
                <?php endif; ?>
                <br>
            </li>
        <?php endforeach; ?>
    </ul>

    <a href="admin.php">Admin Dashboard</a>
</body>
</html>
