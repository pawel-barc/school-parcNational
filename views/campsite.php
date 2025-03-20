<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Campings</title>
    <link rel="stylesheet" href="assets/style/user/campsite.css">
    <link rel="stylesheet" href="assets/style/config/_filter.css">
    <link rel="stylesheet" href="assets/style/config/_global.css">
    <script src="assets/script/filter/filter-campsite.js" defer></script>
</head>
<?php
    $campsite_id = $_GET['id'] ?? null;
    $vacationEvents = [];
    if ($campsite_id) {
        require_once __DIR__ . '/../controllers/CampsiteController.php'; 
        $campsiteController = new CampsiteController();
        $vacationEvents = $campsiteController->getVacationEvents($campsite_id);
    }
    ?>
<body>
    <main>
        <section>
            <div class="hero-page">
                <header><?php include "components/_header.php"; ?></header>    
                <hgroup class="text-overlay">
                    <h1 class="title-page">Les Campings</h1>
                    <p>
                        Profitez de la nature, des paysages méditerranéens et d'une expérience inoubliable dans le sud de la France en
                        séjournant dans l'un nos campings proches des calanques, offrant un cadre naturel et paisible pour vos vacances.
                    </p>
                </hgroup>
            </div>
        </section>

        <section class="title-and-toggle-container">
            <h2>Séjournez dans un camping près des Calanques</h2>
            <div class="container-toggle">
                <p>Uniquement campings ouverts</p>
                <label class="toggle">
                    <input class="toggle-checkbox" type="checkbox" id="toggle">
                    <span class="toggle-switch"></span>
                </label>
            </div>
        </section>
        
        <?php if (!empty($campsites)): ?>
            <div>
                <?php foreach ($campsites as $campsite): ?>
                <div class="campsite-status" id="campsite-status-<?= $campsite['campsite_id']; ?>"></div>
            </div>
                <?php endforeach; ?>
        <?php else: ?>
            <p>Aucun camping trouvé.</p>
        <?php endif; ?>
        
    </main>
    <section id="overflow" class="camping-grid">
        <!-- Les éléments seront injectés ici -->
    </section>

    <footer>
        <?php include __DIR__ . '/../components/_footer.php'; ?>
    </footer>
    <script>    
        let vacationEvents = <?= json_encode($vacationEvents); ?>;
        let campsiteStatuses = <?php echo json_encode(array_map(function($campsite) {
            return ['id' => $campsite['campsite_id'], 'status' => $campsite['status']];
        }, $campsites)); ?>;
    </script>
</body>
</html>
