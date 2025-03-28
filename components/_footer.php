<link rel="stylesheet" href="assets/style/config/_footer.css">
<link rel="stylesheet" href="assets/style/config/_global.css">

<nav class="nav-footer">
    <section class="first-section-footer">

        <div class="title-logo-container">
            <img class="logo-footer" src="assets/img/logo-pncal.jpg" alt="Logo du Parc Nationnal des Calanques">
            <li>Parc National des Calanques</li>
        </div>
        <div class="icon-container-footer">
            <img src="assets/icon/facebook.svg" alt="Icon Facebook">
            <img src="assets/icon/instagram.svg" alt="Icon Instagram">
            <img src="assets/icon/whatsapp.svg" alt="Icon Whatsapp">
            <img src="assets/icon/tiktok.svg" alt="Icon TikTok">
            <img src="assets/icon/linkedin.svg" alt="Icon Linkedin">
            <a class="top-icon" href="#"><img src="assets/icon/arrow_up.svg" alt="Icon Arrow Up"></a>
        </div>
    </section>

    <section class="second-section-footer">
        <div>
            <div class="left-part">
                <ul class="ul-footer">
                    <li class="ul-title"><b>Navigation</b></li>
                    <br>
                    <li><a href="home">Accueil</a></li>
                    <li><a href="ressources">Les Ressources naturelles</a></li>
                    <li><a href="trails">Les Sentiers</a></li>
                    <li><a href="campsite">Les Campings</a></li>
                    <li><a href="map">La Carte</a></li>
                    <?php if (!isset($_SESSION['user_id'])): ?>
                        <li><a href="log">Connexion</a></li>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="profile">Profil</a></li>
                        <li><a href="logout">Se déconnecter</a></li>
                    <?php endif; ?>
                </ul>
                <ul class="ul-footer">
                    <li class="ul-title"><b>Coordonnées</b></li>
                    <br>
                    <li>13008 Marseille</li>
                    <li>141 avenue du Prado</li>
                    <li>Tel : 04 20 10 50 00</li>
                    <li>Email : <br>info@calanques-park.com</li>
                </ul>
                <ul class="ul-footer">
                    <li class="ul-title"><b>A propos</b></li>
                    <br>
                    <li><a href="about">Qui sommes-nous ?</a></li>
                    <li><a href="about">Protections des données</a></li>
                    <li><a href="about">Règles et conditions</a></li>
                </ul>
            </div>
            </div>
        <div class="weather-container-footer">
            <?php include 'components/_weather.php' ?>
        </div>
    </section>

    <section class="thrid-section-footer">
        <p>© Parc National des Calanques. 2024. Tous droits réservés.</p>  
    </section>

    <script>
        // Ajoute un événement au clic sur l'icône avec la classe "top-icon"
        document.querySelector('.top-icon').addEventListener('click', function(event) {
            event.preventDefault(); // Empêche le comportement par défaut et la mise à jour de l'URL
            window.scrollTo({
                top: 0,
                behavior: 'smooth' // Active le défilement fluide
            });
        });
        // Exécute le script une fois que le DOM est entièrement chargé
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelectorAll(".ul-title").forEach(title => {
                title.addEventListener("click", function () {
                    // Bascule la classe "open" sur l'élément parent pour afficher/masquer son contenu
                    this.parentElement.classList.toggle("open");
                });
            });
        });
    </script>
</nav>