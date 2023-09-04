<?php
session_start();
if (!isset($_SESSION['email']) || $_SESSION['role'] !== 'editor') {
    header('Location: login.php');
    exit;
}

$connect = mysqli_connect("localhost", "root", "", "uppics");
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

if (isset($_POST['deleteImage'])) {
    $imageId = $_POST['imageId'];

    $query = "SELECT * FROM images WHERE id = ?";
    $stmt = mysqli_prepare($connect, $query);
    mysqli_stmt_bind_param($stmt, "i", $imageId);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    $imageFileName = $row['image_filename'];

    $deleteQuery = "DELETE FROM images WHERE id = ?";
    $deleteStmt = mysqli_prepare($connect, $deleteQuery);
    mysqli_stmt_bind_param($deleteStmt, "i", $imageId);
    mysqli_stmt_execute($deleteStmt);

    unlink("uploads/" . $imageFileName);
}

if (isset($_POST['uploadImage'])) {
    $allowedFormats = array('image/png', 'image/jpeg');
    $imageFormat = $_FILES['image']['type'];

    if (in_array($imageFormat, $allowedFormats)) {
        $imageFileName = $_FILES['image']['name'];
        $imageFilePath = 'uploads/' . $imageFileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $imageFilePath)) {
            $email = $_SESSION['email'];
            $query = "INSERT INTO images (`email`, `image_filename`) VALUES (?, ?)";
            $stmt = mysqli_prepare($connect, $query);
            mysqli_stmt_bind_param($stmt, "ss", $email, $imageFileName);

            if (mysqli_stmt_execute($stmt)) {
                echo '<script>
                alert("Image uploaded successfully");
                window.location.href = "editor.php";
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
        window.location.href = "editor.php";
        </script>';
        exit;
    }
}


$query = "SELECT images.*, users.email AS uploader_email
            FROM images
            INNER JOIN users ON images.email = users.email";
$result = mysqli_query($connect, $query);
$images = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editor Dashboard</title>
</head>
<body>
    <h1>Editor Dashboard</h1>
    <p>Welcome Editor, <?php echo $_SESSION['email']; ?></p>

    <h2>Upload New Image</h2>
    <form method="POST" enctype="multipart/form-data">
        <label for="image">Upload an image (PNG or JPEG):</label>
        <input type="file" name="image" accept="image/png,image/jpeg" required>
        <input type="submit" name="uploadImage" value="Upload">
    </form>
    <br><br>
    <h2>Delete Uploaded Images</h2>
    <ul>
        <?php foreach ($images as $image) : ?>
            <li>
                <p>Uploader: <?php echo $image['uploader_email']; ?></p>
                <img src="uploads/<?php echo $image['image_filename']; ?>" alt="Image">
                <br>
                <?php echo $image['image_filename']; ?>
                <br><br>
                <form method="POST">
                    <input type="hidden" name="imageId" value="<?php echo $image['id']; ?>">
                    <input type="submit" name="deleteImage" value="Delete">
                    <br>
                </form>
            </li>
        <?php endforeach; ?>
    </ul>
    <a href="view.php">View your uploaded pictures</a>
    <br><br>
    <a href="logout.php">Logout</a>
</body>
</html>
