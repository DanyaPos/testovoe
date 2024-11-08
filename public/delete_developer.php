<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "symfony_project";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM developer WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: developer.php");
        exit();
    } else {
        echo "Ошибка: " . $stmt->error;
    }
}

$conn->close();
?>
