<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Initialisation des paramètres de la session
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => 'school-parcnational.local', 
    'httponly' => true, 
]);

 // Démarrage de la session
session_start();
// Régénération de l'ID de la session pour éviter les attaques de fixation de session
session_regenerate_id(true);
// Chargement des dépendances via Composer
require 'vendor/autoload.php';
// Chargement du fichier .env et des variables d'environnement
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

// Chargement des routes depuis le fichier de configuration
$routes = require __DIR__ . "/config/routes.php";
// require_once 'controllers/HomeController.php';
// require_once 'controllers/RessourceController.php';
// Nettoyage de l'URL et traitement de la route
$url = trim($_SERVER['REQUEST_URI'], '/');
$url = str_replace("school-parcNational", '', $url);
$url = trim($url, '/');//Removal of the string 'parkNational' from the link
$urlArray = explode('?', $url);
// Vérification de l'existence de la route
if (isset($routes[$urlArray[0]])) { 
    $className = $routes[$urlArray[0]]['controller'];
    $methodName = $routes[$urlArray[0]]['method'];
    require_once __DIR__ . '/controllers/' . $className . '.php';
    require_once 'controllers/HomeController.php';
    // Création de l'objet en fonction de la classe
    if ($className == 'HomeController') {
        $object = new $className('news');
    } elseif ($className == 'TrailsController') {
        $object = new $className('trails');
    } else {
        $object = new $className; // Pour les autres contrôleurs
    }

    // Vérification si la méthode nécessite un ID
    if ($methodName === 'details_news') {
        // Vérifiez si un ID est passé dans l'URL, par exemple : /details_news?id=123
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $object->{$methodName}($id); // Appel de la méthode avec l'ID
        } else {
            echo "Erreur : ID manquant.";
            return;
        }
    } elseif (in_array($methodName, ['getCampsiteById', 'getRessourceById'])) {
        if (isset($_GET['id'])) {
            $id = (int)$_GET['id'];
            $object->{$methodName}($id); // Appel avec l'ID
        } else {
            echo "Erreur : ID manquant.";
            return;
        }
    } elseif ($methodName === 'getReservationsByUser') {
        $user_id = 1; // Remplacez par l'ID de l'utilisateur connecté
        $object->{$methodName}($user_id); 
    } else {
        $object->{$methodName}();
    }
} else {
    echo "Pas d'adresse valide.";
}