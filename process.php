<?php
$servername = "localhost";
$username = "root";
$password ="";
$dbname ="iot";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $uid = $_POST['uid'];
    
    $sql = "INSERT INTO transactions (uid, timestamp) VALUES ('$uid', NOW())";
    
    if ($conn->query($sql) === TRUE) {
        echo "Transaction recorded successfully";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

$conn->close();
?>
