<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Selection de Produit</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2>Sélectionnez un produit</h2>
        <form action="read tag_privé.php" method="get">
            <div class="form-group">
                <label for="product_id">ID du produit :</label>
                <input type="number" id="product_id" name="product_id" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-primary">Acheter</button>
        </form>
    </div>
</body>
</html>





<?php
    // Si le produit n'est pas spécifié, retour à la sélection
    if (!isset($_GET['product_id'])) {
        header("Location: index.php");
        exit();
    }

    $product_id = $_GET['product_id'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <title>Scanner la Carte</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            setInterval(function() {
                $("#getUID").load("UIDContainer.php");
            }, 500);
        });

        function validatePurchase() {
            var userId = document.getElementById("getUID").innerHTML;
            window.location.href = "read tag user data_privé.php?id=" + userId + "&product_id=<?php echo $product_id; ?>";
        }

        $(document).on('DOMSubtreeModified', '#getUID', function() {
            var userId = document.getElementById("getUID").innerHTML;
            if (userId !== "") {
                validatePurchase();
            }
        });
    </script>
</head>
<body>
    <div class="container">
        <h2>Scannez votre carte</h2>
        <p id="getUID" hidden></p>
    </div>
</body>
</html>






<?php
require 'database.php';

$id = null;
$product_id = null;
$msg = null;

if (!empty($_GET['id']) && !empty($_GET['product_id'])) {
    $id = $_GET['id'];
    $product_id = $_GET['product_id'];

    try {
        // Connexion à la base de données
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Récupérer le prix du produit
        $sql = "SELECT prix FROM produit WHERE id = ?";
        $q = $pdo->prepare($sql);
        $q->execute(array($product_id));
        $product = $q->fetch(PDO::FETCH_ASSOC);

        if ($product === false) {
            $msg = "Produit non trouvé!";
        } else {
            $prix_produit = $product['prix'];

            // Récupérer le solde de l'utilisateur
            $sql = "SELECT solde FROM uid WHERE id = ?";
            $q = $pdo->prepare($sql);
            $q->execute(array($id));
            $user = $q->fetch(PDO::FETCH_ASSOC);

            if ($user === false) {
                $msg = "Utilisateur non trouvé.";
            } else {
                $solde = $user['solde'];

                // Vérifier si le solde est suffisant pour l'achat
                if ($solde >= $prix_produit) {
                    // Mettre à jour le solde de l'utilisateur
                    $nouveau_solde = $solde - $prix_produit;
                    $sql = "UPDATE uid SET solde = ? WHERE id = ?";
                    $q = $pdo->prepare($sql);
                    $q->execute(array($nouveau_solde, $id));

                    $msg = "Achat réussi! Nouveau solde: " . $nouveau_solde;
                } else {
                    $msg = "Solde insuffisant pour effectuer l'achat.";
                }
            }
        }

        Database::disconnect();
    } catch (PDOException $e) {
        $msg = "Erreur de base de données: " . $e->getMessage();
    }
} else {
    $msg = "ID de produit ou ID utilisateur non fourni.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <script src="js/bootstrap.min.js"></script>
</head>
<body>
    <div class="container">
        <h2>Résultat de l'achat</h2>
        <p><?php echo htmlspecialchars($msg); ?></p>
    </div>
</body>
</html>
