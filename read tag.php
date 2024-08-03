<?php
require 'database.php';

$id = null;
$msg = null;

try {
    if (!empty($_GET['id'])) {
        $id = $_GET['id'];
        $product_id = isset($_GET['product_id']) ? $_GET['product_id'] : null;

        // Connexion à la base de données avec PDO
        $pdo = Database::connect();
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        // Vérification de l'ID de l'utilisateur
        $sql = "SELECT * FROM uid WHERE id = ?";
        $q = $pdo->prepare($sql);
        $q->execute(array($id));
        $data = $q->fetch(PDO::FETCH_ASSOC);

        if ($data === false) {
            $msg = "L'ID de votre carte/clé n'est pas enregistré!";
            $data = array(
                'id' => $id,
                'name' => "--------"
            );
        } else if ($product_id) {
            // Vérification de l'ID du produit
            $sql = "SELECT prix FROM produit WHERE id = ?";
            $q = $pdo->prepare($sql);
            $q->execute(array($product_id));
            $product = $q->fetch(PDO::FETCH_ASSOC);

            if ($product === false) {
                $msg = "Produit non trouvé!";
            } else {
                $prix_produit = $product['prix'];
                echo "Prix produit: $prix_produit<br>";

                // Vérification du solde de l'utilisateur
                $sql = "SELECT solde FROM uid_scanne WHERE uid = ?";
                $q = $pdo->prepare($sql);
                $q->execute(array($id));
                $user = $q->fetch(PDO::FETCH_ASSOC);

                if ($user === false) {
                    $msg = "Utilisateur non trouvé.";
                } else {
                    $solde = $user['solde'];
                    echo "Solde utilisateur: $solde<br>";

                    // Vérification du solde suffisant
                    if ($solde >= $prix_produit) {
                        // Mise à jour du solde de l'utilisateur
                        $nouveau_solde = $solde - $prix_produit;
                        $sql = "UPDATE uid_scanne SET solde = ? WHERE uid = ?";
                        $q = $pdo->prepare($sql);
                        $q->execute(array($nouveau_solde, $id));

                        $msg = "Achat réussi!";
                        echo "Achat réussi! Nouveau solde: $nouveau_solde<br>";
                    } else {
                        $msg = "Solde insuffisant pour effectuer l'achat.";
                        echo "Solde insuffisant pour effectuer l'achat.<br>";
                    }
                }
            }
        } else {
            $msg = "ID de produit non fourni";
        }

        Database::disconnect();
    } else {
        $data = array(
            'id' => "N/A",
            'name' => "--------"
        );
        $msg = "Aucun ID fourni!";
    }
} catch (PDOException $e) {
    $msg = "Erreur de base de données: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <script src="js/bootstrap.min.js"></script>
    <style>
        td.lf {
            padding-left: 15px;
            padding-top: 12px;
            padding-bottom: 12px;
        }
    </style>
</head>
<body>
    <div>
        <form>
            <table width="452" border="1" bordercolor="#10a0c5" align="center" cellpadding="0" cellspacing="1" bgcolor="#000" style="padding: 2px">
                <tr>
                    <td height="40" align="center" bgcolor="#10a0c5"><font color="#FFFFFF"><b>Données de l'utilisateur</b></font></td>
                </tr>
                <tr>
                    <td bgcolor="#f9f9f9">
                        <table width="452" border="0" align="center" cellpadding="5" cellspacing="0">
                            <tr bgcolor="#f2f2f2">
                                <td align="left" class="lf">Nom</td>
                                <td style="font-weight:bold">:</td>
                                <td align="left"><?php echo htmlspecialchars($data['name']); ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <p style="color:red;"><?php echo htmlspecialchars($msg); ?></p>
</body>
</html>
