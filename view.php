<?php
session_start();

if (!isset($_SESSION['email'])) {
    header('Location: login.php'); 
    exit;
}

$role = $_SESSION['role'];

$connect = mysqli_connect("localhost", "root", "", "uppics");
if (!$connect) {
    die("Connection failed: " . mysqli_connect_error());
}

$email = $_SESSION['email'];
$query = "SELECT * FROM images WHERE email = '$email'";
$result = mysqli_query($connect, $query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Images</title>
</head>
<body>
    <h1><?php echo $_SESSION['email']; ?>'s uploaded pictures</h1>
    <?php while ($row = mysqli_fetch_assoc($result)) : ?>
        <?php $imageFileName = $row['image_filename']; ?>
        <p><?php echo $imageFileName; ?></p>
        <img src="uploads/<?php echo $imageFileName; ?>" alt="Uploaded Image">
        <br>
        <br>
    <?php endwhile; ?>
    
    <?php
    if ($role === 'editor') {
        echo '<a href="editor.php">Editor Dashboard</a>';
    } elseif ($role === 'admin') {
        echo '<a href="admin.php">Admin Dashboard</a>';
    } else {
        echo '<a href="home.php">Home</a>';
    }
    ?>
</body>
</html>
