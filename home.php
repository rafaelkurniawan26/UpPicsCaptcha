<?php
session_start();
if (!isset($_SESSION['email'])) {
    header('Location: login.php'); 
    exit;
}

$connect = mysqli_connect("localhost", "root", "", "uppics");
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['Upload'])) {
    $allowedFormats = array('image/png', 'image/jpeg');
    $imageFormat = $_FILES['image']['type'];

    if (in_array($imageFormat, $allowedFormats)) {
        $imageFileName = $_FILES['image']['name'];
        $imageFilePath = 'uploads/' . $imageFileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $imageFilePath)) {
            $email = $_SESSION['email'];
            $query = "INSERT INTO images (`email`, `image_filename`) VALUES (?, ?);";
            $stmt = mysqli_prepare($connect, $query);
            mysqli_stmt_bind_param($stmt, "ss", $email, $imageFileName);

            if (mysqli_stmt_execute($stmt)) {
                echo '<script>
                alert("Image uploaded successfully");
                window.location.href = "home.php";
                </script>';
                exit;
            } else {
                echo "Error: " . mysqli_error($connect);
            }

            mysqli_stmt_close($stmt);
        } else {
            echo "Error uploading the image.";
        }
    } else {
        echo '<script>
        alert("Only PNG and JPEG images are allowed");
        window.location.href = "home.php";
        </script>';
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
</head>
<body>
    <h1>Welcome, <?php echo $_SESSION['email']; ?>!</h1>
    <form method="POST" enctype="multipart/form-data">
        <label for="image">Upload an image (PNG or JPEG):</label>
        <input type="file" name="image" accept="image/png,image/jpeg" required>
        <input type="submit" name="Upload" value="Upload">
    </form>
    <br>
    <br>
    <a href="view.php">View your uploaded pictures</a>
    <br>
    <br>
    <a href="logout.php">Logout</a>
</body>
</html>
