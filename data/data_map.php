<?php 
include '../config/connectBDD.php';
include '../models/Trails.php';

//! Instancier la classe ConnectBDD et obtenir la connexion PDO
$connectBDDInstance = new ConnectBDD();
$connectBDD = $connectBDDInstance->bdd; // Assurez-vous que cette variable contient une connexion valide

//! Instancier la classe Trails et obtenir les données des sentiers
$trailsModel = new Trails($connectBDD); // Passer la connexion à Trails

// Si ce fichier est appelé directement, afficher les résultats
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json; charset=utf-8'); // Indique que le contenu est du JSON
    
    //! Appeler la fonction get_map_data pour récupérer les données de la map
    //? imporant de mettre l'instace et d'appeller la fonction !! il faut la flèche sinon ca ne marche pas avec le MVC
    $data = $trailsModel->get_map_data($connectBDD);
    
    // S'assurer que les données sont présentes
    if ($data && isset($data['type']) && $data['type'] === 'FeatureCollection') {
        // Encoder en JSON avec JSON_PRETTY_PRINT pour un affichage lisible
        echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        // Gérer les erreurs ou données manquantes
        echo json_encode(["error" => "Les données GeoJSON sont manquantes ou invalides."]);
    }
}
?>
