<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmer l'Achat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f2f2f2;
        }
        .container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h1 {
            margin-bottom: 20px;
        }
        .product-info {
            margin-bottom: 20px;
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
        .actions button {
            padding: 10px 20px;
            margin: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .actions button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Confirmer l'Achat</h1>
        <div class="product-info">
            <h2><?php echo $product_name; ?></h2>
            <p class='product-price'><?php echo $product_price; ?> €</p>
        </div>
        <div class="actions">
            <button onclick="window.location.href='index.php';">Annuler</button>
            <button onclick="confirmPurchase(<?php echo $product_id; ?>)">Je confirme</button>
        </div>
    </div>

    <script>
        function confirmPurchase(productId) {
            alert("Veuillez scanner votre carte RFID pour finaliser l'achat.");
            // Stockez l'ID du produit dans une variable globale pour utilisation ultérieure
            window.productId = productId;
        }

        // Fonction pour gérer le scan RFID et envoyer les données au serveur
        function handleRFIDScan(uid) {
            // Envoyer les données RFID au serveur
            fetch('process_purchase.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    product_id: window.productId,
                    uid: uid
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Achat réussi !");
                } else {
                    alert("Achat échoué.");
                }
            })
            .catch(error => {
                console.error('Erreur:', error);
                alert("Une erreur est survenue.");
            });
        }
    </script>
</body>
</html>
