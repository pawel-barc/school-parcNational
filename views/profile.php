<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil</title>
    <link rel="stylesheet" href="assets/style/profile.css">
</head>
<body>
    <header>
        <?php include "components/_header.php"; ?>
    </header>
    <h1>Bienvenue sur ton profile <?=$userId['firstname']?></h1>
    <section>
        <!-- Top section containing personal data and memberships -->
        <div class="top-container">
            <!-- User personal data -->
            <div class="user-info-parent">
                <div class="profil-header">
                    <h2 class="title"> Mes données personnelles</h2>
                    <a href="profile-form">
                        <h4>Mettre à jour</h4>
                    </a>
                </div>
                <div class="user-info">
                    <div class="user-info-left">
                        <p><strong class="first">Nom :</strong><?= htmlspecialchars($userId['lastname']??''); ?></p>
                        <p><strong  class="second">Prénom :</strong><?= htmlspecialchars($userId['firstname']??''); ?></p>
                        <p><strong class="third">Téléphone :</strong><?= htmlspecialchars($userId['phone']??''); ?></p>
                    </div>
                    <div class="user-info-right">
                        <p><strong class="fourth">Adresse :</strong><?= htmlspecialchars($userId['address']??''); ?></p>
                        <p><strong class="fifth">Ville : </strong><?= htmlspecialchars($userId['city']??''); ?></p>
                        <p><strong class="sixt">Code postal :</strong><?= htmlspecialchars($userId['zipcode']??''); ?></p>
                    </div>
                </div>
            </div>
            <!-- Existing membership information -->
            <div class="membership">
                <h2> Mon adhésion</h2>
                <?php if(!empty($availableMembership)): ?>
                    <div class="subscription">
                        <p> Mdme/M : 
                        <?php
                            echo !empty($availableMembership['lastname']) ? htmlspecialchars($availableMembership['lastname']) : 'N/A';
                        ?></p>       
                        <p>Identificateur: 
                        <?php
                            echo htmlspecialchars($availableMembership['random_id']); 
                        ?></p>
                        <p>Date de début: 
                        <?php 
                            $deliveryDate = new DateTime($availableMembership['delivery_date']);
                            echo htmlspecialchars($deliveryDate->format('Y-m-d H:i')); 
                        ?>
                        </p>
                        <p>Date d'expiration: 
                        <?php 
                            $expiryDate = new DateTime($availableMembership['expiry_date']);
                            echo htmlspecialchars($expiryDate->format('Y-m-d H:i'));
                        ?>
                        </p>
                    </div>
                <?php endif; ?>  
            </div>
        </div>
    </section>
    <!-- Middle section containing reservations and favorite trails -->
    <section>
        <div class="bottom-container">
            <!-- Reservations section -->
            <div class="left-part">
                <div class="history-link">
                    <h2 class="title-reservation">Mes résérvations</h2>
                    <a class="reservation-button" href="http://school-parcnational.local//reservation_history?campsite_id=1&status=success">Historique de réservations</a>
                </div>
                <div class="reservation-container">
                    <?php if(!empty($reservedCampings)): ?>
                        <?php foreach($reservedCampings as $reservedCamping): ?>
                            <div class="reserved-campings">
                                <a href="campsiteDetails?id=<?= htmlspecialchars($reservedCamping['campsite_id']) ?>">
                                    <img src= <?= $reservedCamping['campsite_image']; ?> alt="Image du camping" class="reservation-image"></img>
                                </a>
                                <p><strong>Nom de camping : </strong><?= htmlspecialchars($reservedCamping['campsite_name']) ; ?></p>
                                <p><strong>Date de debout : </strong><?= date('Y-m-d', strtotime($reservedCamping['start_date'])) ; ?></p>
                                <p><strong>Date de fin : </strong><?= date('Y-m-d', strtotime($reservedCamping['end_date'])) ; ?></p>
                                <p><strong>Prix : </strong><?= htmlspecialchars($reservedCamping['price']) ; ?> €</p>
                                <p><strong>Date de résérvation : </strong><?= htmlspecialchars($reservedCamping['reservation_date']) ; ?></p>
                                <p><strong>Statut : </strong><?= htmlspecialchars($reservedCamping['status']) ; ?></p>

                            </div>
                        <?php endforeach; ?>
                        <?php else: ?>
                            <p>Pas de résérvations</p>
                    <?php endif; ?>
                </div>
                <!-- Completed trails section -->
                <div class="completed-trails">
                    <h2 class="trials-title">Mes Sentiers complétes</h2>
                    <div class="image-complet-container">
                        <?php if(!empty($completedTrails)): ?>
                            <?php foreach($completedTrails as $completedTrail): ?>
                                <div class="trail-item">
                                    <div class="trail-image">
                                        <a href="details_trails?id=<?= htmlspecialchars($completedTrail['trail_id']) ?>">
                                            <img class="img-trail" src=<?= htmlspecialchars($completedTrail['image']) ?> alt="la photo de sentier">
                                        </a>
                                        <a href="/manage-completed-trail?trail_id=<?= htmlspecialchars($completedTrail['trail_id']) ?>">
                                            <p class="delete">Supprimer</p>
                                        </a>
                                    </div>
                                    <div class="trail-description">
                                        <p class="description-text"><?= htmlspecialchars($completedTrail['name']) ?> </p>
                                        <div class="lien-container">
                                        </div>
                                    </div>
                                </div>  
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>Pas de sentiers dans le favoris</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <!-- Favorite trails section -->
            <div class="favorites-trails">
                <h2 class="favorites-title">Mes favoris Sentiers</h2>
                <div class="image-container">
                    <?php if(!empty($favoriteTrails)): ?>
                        <?php foreach($favoriteTrails as $trail): ?>
                            <div class="trail-image">
                                <a href="details_trails?id=<?= htmlspecialchars($trail['trail_id']) ?>">
                                    <img class="img-trail" src=<?= htmlspecialchars($trail['image']) ?> alt="la photo de sentier">
                                </a>
                                <div class="lien-container">
                                    <a href="/manage-favorite-trail?trail_id=<?= htmlspecialchars($trail['trail_id']) ?>">
                                        <p class="delete">Supprimer</p>
                                    </a>
                                </div>    
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                    <p>Pas de sentiers dans le favoris</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
    <footer>
      <?php include "components/_footer.php"; ?>
    </footer>
    <script>
        const userData = <?= json_encode($userData, JSON_HEX_TAG); ?>;
    </script>
</body>
</html>