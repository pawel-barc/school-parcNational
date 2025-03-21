<?php

require_once 'Controller.php';
require_once __DIR__ . '/../models/FavoriteTrail.php';
require_once __DIR__ . '/../models/Trails.php';
require_once __DIR__ . '/../models/Membership.php';
require_once __DIR__ . '/../models/CompletedTrails.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/ReservationModel.php';


class ProfileController extends Controller
{
    public function viewProfile()
    {
        if (!isset($_SESSION['user_id'])) {
            $this->render('login');
            exit;
        }

        
         //Récupération des sentiers favoris de l'utilisateur actuel pour les afficher sur la page de profil 
        $favoriteTrailsObject = new FavoriteTrail('favorites_trails');
        $favoriteTrails = $favoriteTrailsObject->getFavoriteTrailByUser($_SESSION['user_id']);
       
        //Récupération des détails de l'adhésion active de l'utilisateur actuel pour les afficher sur la page de profil
        $membershipObject = new Membership('membership');
        $availableMembership = $membershipObject->getMembershipByUserId($_SESSION['user_id']);

        // Récupération des sentiers complétés par l'utilisateur actuel pour les afficher sur la page de profil
        $completedTrailObject = new CompletedTrails('completed_trails');
        $completedTrails = $completedTrailObject->getCompletedTrailByUser();

        //Récupération des informations de l'utilisateur actuel pour les afficher sur la page de profil
        $userId = $_SESSION['user_id'];
        $User = new User('users');
        $userId = $User->getById($userId);

        //Récupération des réservations de l'utilisateur actuel pour les afficher sur la page de profil
        $user_id = $_SESSION['user_id'];
        $reservedCampingObject = new User('c.campsite_id');
        $reservedCampings = $reservedCampingObject->getReservationsByUser($user_id);
        require_once __DIR__ . '/../views/profile.php';
    }

    public function ProfileForm(){
        if (!isset($_SESSION['user_id'])) {
        $this->render('login');
        exit;
    }
    // Récupération des informations enregistrées de l'utilisateur et affichage dans le formulaire de profil
    $userId = $_SESSION['user_id'];
    $userModel = new User('users');
    $userData = $userModel->getById($userId);

    $this->render('profileForm', ['userData' => $userData]);
    }


    public function updateProfile(){
        if (!isset($_SESSION['user_id'])) {
        $this->render('login');
        exit;
    }

    //Préparation d'un tableau contenant les données mises à jour de l'utilisateur depuis le formulaire 
    $userId = $_SESSION['user_id'];
    $userModel = new User('users');
    $userData = $userModel->getById($userId);
    $updatedData = [
        'firstname' => htmlspecialchars(trim($_POST['firstname'])),
        'lastname' => htmlspecialchars(trim($_POST['lastname'])),
        'gender' => $_POST['gender'] ?? $userData['gender'],
        'phone' => htmlspecialchars(trim($_POST['phone'])),
        'address' => htmlspecialchars(trim($_POST['address'])),
        'city' => htmlspecialchars(trim($_POST['city'])),
        'zipcode' => htmlspecialchars(trim($_POST['zipcode'])),
        'mail' => filter_var($_POST['mail'], FILTER_VALIDATE_EMAIL)
    ];

    if (!empty($_POST['password'])) {
        if($_POST['password'] === $_POST['repeatpassword']){
            $updatedData['password'] = password_hash($_POST['password'], PASSWORD_BCRYPT);
        }else{
            $errorMessage = "Les mots de passe saisis sont différents";
            $this->render('profileForm', ['userData' => $userData, 'errorMessage' => $errorMessage]);
            return;
        }
    } else {
        $updatedData['password'] = $userData['password'];
    }

    // Mise à jour des données utilisateur avec la méthode updateUser 
    $userModel = new User('users');
    $userModel->updateUser($userId, $updatedData);
    $_SESSION['firstname'] = $updatedData['firstname'];
    $_SESSION['lastname'] = $updatedData['lastname'];
    $_SESSION['gender'] = $updatedData['gender'];
    $_SESSION['phone'] = $updatedData['phone'];
    $_SESSION['address'] = $updatedData['address'];
    $_SESSION['city'] = $updatedData['city'];
    $_SESSION['zipcode'] = $updatedData['zipcode'];
    $_SESSION['mail'] = $updatedData['mail'];
    
    // Redirection vers la page de profil après la mise à jour des données
    header('Location: profile');
    exit;
    }

}