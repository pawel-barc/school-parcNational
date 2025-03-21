<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once('Controller.php');
require_once __DIR__ . '/../models/Membership.php';
require_once __DIR__ . '/../models/User.php';

class AdminMembershipController extends Controller
{
    // Afficher la liste des adhésions
    public function viewMembership()
    {
        // Vérifier si l'utilisateur est un administrateur
        $this->checkAdmin();
        if (isset($_SESSION['user_id'])) {
            $membership = new Membership('membership');

            // Récupérer toutes les adhésions
            $allMemberships = $membership->getAllMemberships();

            // Récupérer le nombre total d'adhésions
            $totalMemberships = $membership->getTotalMemberships();

            // Récupérer une adhésion aléatoire
            $randomMembership = !empty($allMemberships) ? $allMemberships[array_rand($allMemberships)] : null;

            // Passer les données à la vue
            $this->render('manage_ships', [
                'memberships' => $allMemberships,
                'totalMemberships' => $totalMemberships,
                'randomMembership' => $randomMembership
            ]);
        } else {
            $this->redirect('login');
        }
    }

    // Ajouter une nouvelle adhésion
    public function addMembership()
    {
        $this->checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $membershipsName = $_POST['memberships_name'];
            $duration = $_POST['duration'];
            $price = $_POST['price'];
            $membershipModel = new Membership('membership');
            $membershipModel->addMembership($membershipsName, $duration, $price);
            $this->redirect('admin-memberships-list');
        } else {
            $this->render('create_ships');
        }
    }

    // Modifier une adhésion existante
    public function editMembership()
    {
        $this->checkAdmin();
        $membershipModel = new Membership('membership');
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $membershipsName = $_POST['memberships_name'];
            $membershipId = $_POST['card_id'];
            $duration = $_POST['duration'];
            $price = $_POST['price'];

            $membershipModel->updateMembership($membershipsName, $duration, $price, $membershipId);
            $this->redirect('admin-memberships-list');
        } else {
            $membershipId = $_GET['id'];
            $membership = $membershipModel->getMembershipById($membershipId);
            $this->render('adminMembershipForm', ['membership' => $membership]);
        }
    }


    // Supprimer une adhésion
    public function deleteMembership()
    {
        $this->checkAdmin();
        $membershipId = $_GET['id'];
        $membershipModel = new Membership('membership');
        $membershipModel->deleteMembership($membershipId);
        $this->redirect('admin-memberships-list');
    }

    // Afficher les adhésions actives
    public function viewActiveMemberships()
    {
        $this->checkAdmin();
        $membership = new Membership('membership');
        $activeMemberships = $membership->getAllActiveMembershipsForAdmin();
        $this->render('adminActiveMembershipsList', ['activeMemberships' => $activeMemberships]);
    }

}
