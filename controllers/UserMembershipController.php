<?php

require_once __DIR__ . '/../vendor/autoload.php';
use Stripe\Stripe;
use Stripe\Checkout\Session;
require_once ('Controller.php');
require_once __DIR__ . '/../models/Membership.php';
require_once __DIR__ . '/../models/User.php';

class UserMembershipController extends Controller{
    public function __construct() {
        // Chargement des variables d'environnement (clés Stripe, etc.)
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();
        Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
    }
    
    public function addMember(){
        // Affichage de la page d'ajout d'abonnement
        $this->render('addMembership');
    }
    
    public function createCheckoutSession($membershipMonths, $price) {
        try {
            // Création d'une session Stripe Checkout pour le paiement
            $checkout_session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => 'Membership ' . $membershipMonths . ' months',
                        ],
                        'unit_amount' => $price * 100, // Prix en centimes
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => 'http://school-parcnational.local/payment-success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => 'http://school-parcnational.local/payment-failed',
            ]);
            // Redirection vers la page de paiement Stripe
            header("Location: " . $checkout_session->url);
            exit();

        } catch (Exception $e) {
            // Gestion des erreurs en cas d'échec de la session Stripe
            error_log("Erreur lors de la création de la session Stripe: " . $e->getMessage());
            echo "Une erreur s'est produite lors du paiement.";
        }
    }
    public function subscribeMembership() {
        if (!isset($_SESSION['user_id'])) {
            // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
            $this->redirect('login');
            exit;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $membershipId = $_POST['membership_id'];
            $membership = new Membership('membership');
            $memberships = $membership->getAllMemberships(); 
            $activeMembership = $membership->getAllActiveMemberships($_SESSION['user_id']);
            // Vérifier si l'utilisateur possède déjà un abonnement actif
            if ($activeMembership) {
                $this->render('viewAvailableMemberships', [
                    'message' => 'Vous avez déjà un abonnement actif.',
                    'memberships' => $memberships 
                ]); 
                return;
            } else {
                // Récupération des détails de l'abonnement sélectionné
                $membershipDetails = $membership->getMembershipById($membershipId);
                if ($membershipDetails) {
                    $durationInMonths = $membershipDetails['duration'];
                    $endDate = new DateTime();
                    $endDate->modify("+$durationInMonths months");
                    $userId = $_SESSION['user_id'];
                    $userModel = new User('users');
                    $user = $userModel->getById($userId);
                    
                    if ($user) {
                        // Stockage des informations de l'utilisateur et de l'abonnement en session
                        $userEmail = $user['mail'];
                        $name = $user['lastname'];
                        $_SESSION['mail'] = $userEmail;
                        $_SESSION['random_id'] = $membershipId; 
                        $_SESSION['expiry_date'] = $endDate->format('Y-m-d');
                        $_SESSION['lastname'] = $name;
                        // Création d'une session de paiement Stripe
                        $this->createCheckoutSession($durationInMonths, $membershipDetails['price']);
                    } else {
                        $this->render('viewAvailableMemberships', [
                            'message' => "L'utilisateur n'a pas été retrouvé.",
                            'memberships' => $memberships
                        ]);
                    }
                } else {
                    $this->render('viewAvailableMemberships', [
                        'message' => "L'adhésion n'a pas été retrouvée.",
                        'memberships' => $memberships
                    ]);
                }
            }
        } else {
            $this->redirect('login');
        }
    }
    
    public function viewMembership() {
        if (isset($_SESSION['user_id'])) {
            // Récupérer l'abonnement actif de l'utilisateur
            $membership = new Membership('membership');
            // Vérifier si l'utilisateur a un abonnement actif
            $currentMembership = $membership->getMembershipByUserId($_SESSION['user_id']);
            if (isset($currentMembership['error'])) {
                $this->render('userMembership', ['message' => $currentMembership['error']]);
            } else {
                $this->render('userMembership', ['membership' => $currentMembership]);
            }
        } else {
            // Redirige vers la page de connexion si l'utilisateur n'est pas connecté
            $this->redirect('login');
        }
    }
    
    public function viewAvailableMemberships() {
        // Récupérer toutes les offres d'abonnement disponibles
        $membership = new Membership('membership');
        $memberships = $membership->getAllMemberships(); 

        if (!$memberships) {
            $memberships = [];
        }
        // Afficher la page avec la liste des abonnements
        $this->render('viewAvailableMemberships', ['memberships' => $memberships]);
        
    }
    
}