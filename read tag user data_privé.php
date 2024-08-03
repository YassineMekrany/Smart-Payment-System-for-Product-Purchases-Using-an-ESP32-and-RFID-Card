<?php
require 'database.php';

$id = isset($_GET['id']) ? $_GET['id'] : null;
$msg = null;
$msg_color = "black"; // Default message color

if (!empty($id)) {
    $pdo = Database::connect();
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $sql = "SELECT * FROM uid WHERE id = ?";
    $q = $pdo->prepare($sql);
    $q->execute(array($id));
    $data = $q->fetch(PDO::FETCH_ASSOC);

    if ($data === false) {
        $msg = "Carte invalide !!!";
        $msg_color = "red";
        $data = array(
            'id' => $id,
            'name' => "--------"
        );
    } else {
        if (isset($_GET['product_id'])) {
            $product_id = $_GET['product_id'];
            $query = "SELECT prix, NOM FROM produit WHERE id = ?";
            $q = $pdo->prepare($query);
            $q->execute(array($product_id));
            $row = $q->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $prix_produit = $row['prix'];
                $product_name = $row['NOM']; // Retrieve product name

                $uid = $id;
                $query = "SELECT solde FROM uid WHERE id = ?";
                $q = $pdo->prepare($query);
                $q->execute(array($uid));
                $row = $q->fetch(PDO::FETCH_ASSOC);

                if ($row) {
                    $solde = $row['solde'];

                    if ($solde >= $prix_produit) {
                        $nouveau_solde = $solde - $prix_produit;
                        $query = "UPDATE uid SET solde = ? WHERE id = ?";
                        $q = $pdo->prepare($query);
                        $q->execute(array($nouveau_solde, $id));
                        $msg = "Achat effectué avec succès !";
                        $msg_color = "green";
                    } else {
                        $msg = "Solde insuffisant pour effectuer l'achat.";
                        $msg_color = "orange";
                    }
                } else {
                    $msg = "Aucun utilisateur trouvé avec cet ID.";
                    $msg_color = "red";
                }
            } else {
                $msg = "Aucun produit trouvé avec cet ID.";
                $msg_color = "red";
            }
        } else {
            $msg = "ID produit n'est pas fourni!";
            $msg_color = "red";
        }
    }
} else {
    $data = array(
        'id' => "N/A",
        'name' => "--------"
    );
    $msg = "No ID provided!";
    $msg_color = "red";
}

// Après avoir vérifié que l'achat est effectué avec succès
if ($msg === "Achat effectué avec succès !") {
    // Récupérer le nombre actuel d'achats pour le produit donné
    $query = "SELECT nbre_achats FROM transactions WHERE product_id = ?";
    $q = $pdo->prepare($query);
    $q->execute(array($product_id));
    $row = $q->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        // Si une ligne est trouvée, récupérez le nombre d'achats actuel
        $nb_achats = $row['nbre_achats'];
        // Incrémente le nombre d'achats
        $nb_achats++;
        // Mettre à jour le nombre d'achats dans la base de données
        $query = "UPDATE transactions SET nbre_achats = ? , uid = ? WHERE product_id = ?";
        $q = $pdo->prepare($query);
        $q->execute(array($nb_achats,$id, $product_id));
    } else {
        // Si aucune ligne n'est trouvée, cela signifie que c'est le premier achat pour ce produit
        // Vous pouvez insérer une nouvelle ligne avec nb_achats à 1
        $query = "INSERT INTO transactions (product_id, product_name, nbre_achats,uid) VALUES (?, ?, 1,?)";
        $q = $pdo->prepare($query);
        $q->execute(array($product_id, $product_name,$id));
    }
}
?>

<!DOCTYPE html>
<html lang="en">
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
                    <td height="40" align="center" bgcolor="#10a0c5"><font color="#FFFFFF"><b>User Data</b></font></td>
                </tr>
                <tr>
                    <td bgcolor="#f9f9f9">
                        <table width="452" border="0" align="center" cellpadding="5" cellspacing="0">
                            <tr bgcolor="#f2f2f2">
                                <td align="left" class="lf">Name</td>
                                <td style="font-weight:bold">:</td>
                                <td align="left"><?php echo htmlspecialchars($data['name']); ?></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </form>
    </div>
    <p style="color:<?php echo htmlspecialchars($msg_color); ?>;"><?php echo htmlspecialchars($msg); ?></p>
    <script>
        // Fonction pour rafraîchir la page toutes les 3 secondes
        setInterval(function() {
            location.reload();
        }, 2000); // 3000 millisecondes = 3 secondes
    </script>
</body>
</html>
