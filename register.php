<?php
session_start();
$connect = mysqli_connect("localhost", "root", "", "uppics");
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['Submit'])) {
    $email = mysqli_real_escape_string($connect, $_POST['email']);
    $password = mysqli_real_escape_string($connect, $_POST['password']);
    $confirmPassword = mysqli_real_escape_string($connect, $_POST['confirmPassword']);
    $minPasswordLength = 8;

    if (strlen($password) < $minPasswordLength) {
        echo '<script>
            alert("Password must be at least 8 characters long");
            window.location.href = "register.php";
        </script>';
        exit;
    }

    if ($password !== $confirmPassword) {
        echo '<script>
            alert("Passwords do not match");
            window.location.href = "register.php";
        </script>';
        exit;
    }

    $selectedRole = $_POST['role'];

    if ($selectedRole === 'admin' || $selectedRole === 'editor') {
        $allowedAdminEmails = array('admin@gmail.com', 'admin123@gmail.com');
        $allowedEditorEmails = array('editor@gmail.com', 'editor123@gmail.com');

        if (($selectedRole === 'admin' && !in_array($email, $allowedAdminEmails)) ||
            ($selectedRole === 'editor' && !in_array($email, $allowedEditorEmails))) {
            echo '<script>
                alert("You are not authorized for the selected role");
                window.location.href = "register.php";
            </script>';
            exit;
        }

        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = mysqli_prepare($connect, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            echo '<script>
                alert("Email already exists");
                window.location.href = "register.php";
            </script>';
            exit;
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $query = "INSERT INTO users (`email`, `password`, `role`) VALUES (?, ?, ?);";
        $stmt = mysqli_prepare($connect, $query);
        mysqli_stmt_bind_param($stmt, "sss", $email, $hashedPassword, $selectedRole);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            mysqli_close($connect);
            header('Location: login.php');
            exit;
        } else {
            echo "Error: " . mysqli_error($connect);
        }
    } elseif ($selectedRole === 'user') {
        $query = "SELECT * FROM users WHERE email = ?";
        $stmt = mysqli_prepare($connect, $query);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            echo '<script>
                alert("Email already exists");
                window.location.href = "register.php";
            </script>';
            exit;
        }

        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $query = "INSERT INTO users (`email`, `password`, `role`) VALUES (?, ?, ?);";
        $stmt = mysqli_prepare($connect, $query);
        mysqli_stmt_bind_param($stmt, "sss", $email, $hashedPassword, $selectedRole);

        if (mysqli_stmt_execute($stmt)) {
            mysqli_stmt_close($stmt);
            mysqli_close($connect);
            header('Location: login.php');
            exit;
        } else {
            echo "Error: " . mysqli_error($connect);
        }
    } else {
        header('Location: login.php');
        exit;
    }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <div class="forms">
    <form method="POST">
        <label for="role">Select Role:</label>
        <select name="role" id="role" required>
        <option value="user">User</option>
        <option value="admin">Admin</option>
        <option value="editor">Editor</option>
        </select>
        <br>
        <br>
        <label for="" id="label">Email</label>
        <br>
        <input type="email" name="email" id="input" required>
        <br>
        <label for="" id="label">Password</label>
        <br>
        <input type="password" name="password" id="input" required>
        <br>
        <label for="" id="label">Confirm Password</label>
        <br>
        <input type="password" name="confirmPassword" id="input" required>
        <br>
        <div class="g-recaptcha" data-sitekey="6Lein_onAAAAAC9TMKl-Dh1FJxfurvctLpIzp0xm"></div>
        <br>
        <br>
        <input type="submit" name="Submit" id="input2">
        Already have an account? <a href="login.php">Login</a>
    </form>
    </div>
</body>
</html>
