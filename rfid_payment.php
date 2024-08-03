<?php
// Activer le rapport d'erreurs
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Consigner les données POST reçues pour le débogage
file_put_contents('php://stderr', print_r($_POST, TRUE));

// Vérifier si les données nécessaires sont présentes
if (!isset($_POST['product_id']) || !isset($_POST['uid'])) {
    echo "Données manquantes.";
    exit;
}

$product_id = intval($_POST['product_id']);
$uid = $_POST['uid'];

// Connexion à la base de données
$servername = "localhost";
$username = "root";
$password = "";
$database = "iot";
$conn = new mysqli($servername, $username, $password, $database);
if ($conn->connect_error) {
    die("La connexion à la base de données a échoué: " . $conn->connect_error);
}

// Insérer la transaction dans la base de données (à adapter selon votre structure)
$sql = "INSERT INTO transactions (product_id, rfid_uid, date) VALUES ($product_id, '$uid', NOW())";
if ($conn->query($sql) === TRUE) {
    echo "Achat réussi";
} else {
    echo "Erreur: " . $conn->error;
}

$conn->close();
?>
