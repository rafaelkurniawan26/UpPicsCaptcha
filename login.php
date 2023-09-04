<?php
session_start();
$connect = mysqli_connect("localhost", "root", "", "uppics");
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}
if (isset($_POST['Login'])) {
    $email = mysqli_real_escape_string($connect, $_POST['email']);
    $password = mysqli_real_escape_string($connect, $_POST['password']);
    $query = "SELECT * FROM users WHERE BINARY email = ?;";
    $stmt = mysqli_prepare($connect, $query);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $hashedPassword = $row['password'];
        if (password_verify($password, $hashedPassword)) {
            $_SESSION['email'] = $email;
            
            $_SESSION['role'] = $row['role'];

            if ($_SESSION['role'] === 'admin') {
                header('Location: admin.php');
                exit;
            } elseif ($_SESSION['role'] === 'editor') {
                header('Location: editor.php');
                exit;
            } else {
                header('Location: home.php'); 
                exit;
            }
        } else {
            echo '<script>
            alert("Login Failed");
            window.location.href = "login.php";
            </script>';
        }
    } else {
        echo '<script>
            alert("Login Failed");
            window.location.href = "login.php";
            </script>';
    }

    mysqli_stmt_close($stmt);
    mysqli_close($connect);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</head>
<body>
    <div class="forms">
    <form method="POST">
        <label for="email" id="label">Email</label>
        <br>
        <input type="email" name="email" id="input" required>
        <br>
        <label for="password" id="label">Password</label>
        <br>
        <input type="password" name="password" id="input" required>
        <br>
        <div class="g-recaptcha" data-sitekey="6Lein_onAAAAAC9TMKl-Dh1FJxfurvctLpIzp0xm"></div>
        <br>
        <br>
        <input type="submit" name="Login" value="Login" id="input2">
        Don't have an account yet? <a href="register.php">Sign Up</a>
    </form>
    </div>
</body>
</html>
