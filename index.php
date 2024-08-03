<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Produits</title>
    <style>
        /* Ajoutez vos styles ici */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f2f2f2;
        }
        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .product {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        .product img {
            max-width: 100px;
            margin-right: 20px;
            border-radius: 5px;
        }
        .product-info {
            flex-grow: 1;
        }
        .product-info h2 {
            margin: 0;
            color: #333;
        }
        .product-info p {
            margin: 5px 0;
            color: #666;
        }
        .product-price {
            font-weight: bold;
            color: #ff5733;
        }
        .buy-button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            margin: 4px 2px;
            cursor: pointer;
            border-radius: 5px;
        }
        .buy-button:hover {
            background-color: #218838;
        }
    </style>
<script>
    function confirmPurchase(productId) {
        if (confirm('Êtes-vous sûr de vouloir acheter ce produit ?')) {
            window.location.href = 'read tag_privé.php?product_id=' + productId;
        }
    }
</script>
</head>
<body>
    <div class="container">
        <h1>Liste des Produits</h1>
        <?php
        // Connexion à la base de données
        $servername = "localhost";
        $username = "root";
        $password = "";
        $database = "iot";
        $conn = new mysqli($servername, $username, $password, $database);
        if ($conn->connect_error) {
            die("La connexion à la base de données a échoué: " . $conn->connect_error);
        }
        // Requête SQL pour récupérer les données des produits
        $sql = "SELECT * FROM produit";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<div class='product'>";
                echo "<img src='iot'>"; // Assurez-vous que le chemin de l'image est correct
                echo "<div class='product-info'>";
                echo "<h2>" . $row["NOM"] . "</h2>";
                echo "<p>" . $row["description"] . "</p>";
                echo "<p class='product-price'>" . $row["prix"] . " €</p>";
                echo "<button class='buy-button' onclick='confirmPurchase(" . $row["id"] . ")'>Acheter</button>";
                echo "</div>";
                echo "</div>";
            }
        } else {
            echo "Aucun produit trouvé.";
        }
        // Fermer la connexion à la base de données
        $conn->close();
        ?>
    </div>
</body>
</html>
