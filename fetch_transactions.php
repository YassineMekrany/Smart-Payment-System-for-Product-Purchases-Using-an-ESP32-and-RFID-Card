<?php
require 'database.php';

$pdo = Database::connect();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$sql = "SELECT product_name, nbre_achats,uid FROM transactions";
$q = $pdo->prepare($sql);
$q->execute();

$transactions = array();
while ($row = $q->fetch(PDO::FETCH_ASSOC)) {
    $transactions[] = $row;
}

header('Content-Type: application/json');
echo json_encode($transactions);
?>
