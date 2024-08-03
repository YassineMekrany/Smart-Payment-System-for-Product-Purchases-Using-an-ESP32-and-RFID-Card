<?php
// Activer le rapport d'erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Récupérer les données POST
$data = json_decode(file_get_contents('php://input'), true);
$product_id = intval($data['product_id']);
$uid = $data['uid'];

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$database = "iot";
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("La connexion à la base de données a échoué: " . $conn->connect_error);
}

// Insérer la transaction dans la base de données
$sql = "INSERT INTO transactions (product_id, rfid_uid, date) VALUES ($product_id, '$uid', NOW())";
$response = array();
if ($conn->query($sql) === TRUE) {
    $response['success'] = true;
} else {
    $response['success'] = false;
    $response['error'] = $conn->error;
}

$conn->close();
echo json_encode($response);
?>
