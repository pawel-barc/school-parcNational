
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="assets/style/user/user-membership.css">
    <title>User Membership</title>
    
</head>
<body>
    <header>
        <?php include "components/_header.php"; ?>
    </header>
    <h2>Votre abonnement:</h2>
    <?php if (isset($membership)): ?>
    <main>    
        <div class="subscription">
            <h3>Votre adhésion active:</h3>
            <p> Mdme/M : 
            <?php
                 echo !empty($membership['lastname']) ? htmlspecialchars($membership['lastname']) : 'N/A';
            ?></p>       
            <p>Identificateur: 
            <?php
                echo htmlspecialchars($membership['random_id']??''); 
            ?></p>
            <p>Date de début: 
            <?php 
                $deliveryDate = new DateTime($membership['delivery_date']??'');
                echo htmlspecialchars($deliveryDate->format('Y-m-d H:i')); 
            ?>
            </p>
            <p>Date d'expiration: 
            <?php 
                $expiryDate = new DateTime($membership['expiry_date']??'');
                echo htmlspecialchars($expiryDate->format('Y-m-d H:i'));
            ?>
            </p>
        </div>
    </main>    
    <?php else: ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>
    <h4><a class="link" href="http://">retour vers home page
    <img class="icon" src="assets/icon/back.svg" alt="icon home"></a>
    </h4>
    <footer>
        <?php include "components/_footer.php"; ?>
    </footer>
</body>
</html>