<?php
require_once __DIR__ . '/../models/Model.php';

class ReservationModel extends Model {

    public function __construct() {
        parent::__construct('reservations');  
    }

    // 1. Créer une réservation
    public function createReservation($user_id, $campsite_id, $start_date, $end_date, $price, $status = "en attente") {
        $query = $this->pdo->prepare('SELECT reservation_id FROM reservations WHERE user_id = :user_id AND campsite_id = :campsite_id AND start_date = :start_date AND end_date = :end_date AND status = "en attente"');
        $query->bindParam(':user_id', $user_id);
        $query->bindParam(':campsite_id', $campsite_id);
        $query->bindParam(':start_date', $start_date);
        $query->bindParam(':end_date', $end_date);
        $query->execute();
    
        // Si une réservation similaire existe, retourner son ID pour éviter la duplication
        if ($existingReservation = $query->fetch(PDO::FETCH_ASSOC)) {
            return $existingReservation['reservation_id'];
        }
    
        // Insérer une nouvelle réservation si aucune réservation en attente similaire n'existe
        $query = $this->pdo->prepare('INSERT INTO reservations (user_id, campsite_id, start_date, end_date, price, reservation_date, status) 
                                     VALUES (:user_id, :campsite_id, :start_date, :end_date, :price, NOW(), :status)');
        $query->bindParam(':user_id', $user_id);
        $query->bindParam(':campsite_id', $campsite_id);
        $query->bindParam(':start_date', $start_date);
        $query->bindParam(':end_date', $end_date);
        $query->bindParam(':price', $price);
        $query->bindParam(':status', $status);
        
        if ($query->execute()) {
            return $this->pdo->lastInsertId();
        } else {
            return false;
        }
    }
            
    // 2. Mettre à jour le statut d'une réservation
    public function updateReservationStatus($reservation_id, $status) {
        $query = $this->pdo->prepare('UPDATE reservations SET status = :status WHERE reservation_id = :reservation_id');
        $query->bindParam(':status', $status);
        $query->bindParam(':reservation_id', $reservation_id);
        return $query->execute();
    }

    // 3. Récupérer toutes les réservations d'un utilisateur
    public function getReservationsByUser($user_id) {
        $query = $this->pdo->prepare('SELECT r.*, c.name AS campsite_name 
                                     FROM reservations r 
                                     JOIN campsite c ON r.campsite_id = c.campsite_id 
                                     WHERE r.user_id = :user_id 
                                     ORDER BY r.reservation_date DESC');
        $query->bindParam(':user_id', $user_id);
        $query->execute();
        return $query->fetchAll(PDO::FETCH_ASSOC);
    }

    // 4. Récupérer une réservation par ID
    public function getReservationById($reservation_id) {
        $query = $this->pdo->prepare('SELECT r.*, c.name AS campsite_name 
                                     FROM reservations r 
                                     JOIN campsite c ON r.campsite_id = c.campsite_id 
                                     WHERE r.reservation_id = :reservation_id');
        $query->bindParam(':reservation_id', $reservation_id);
        $query->execute();
        return $query->fetch(PDO::FETCH_ASSOC);
    }

    // 5. Annuler une réservation
    public function cancelReservation($reservation_id) {
        $query = $this->pdo->prepare('SELECT start_date FROM reservations WHERE reservation_id = :reservation_id');
        $query->bindParam(':reservation_id', $reservation_id);
        $query->execute();
        $reservation = $query->fetch(PDO::FETCH_ASSOC);

        if ($reservation) {
            $start_date = new DateTime($reservation['start_date']);
            $current_date = new DateTime();
            $interval = $current_date->diff($start_date)->days;

            if ($interval >= 7) {
                $query = $this->pdo->prepare('UPDATE reservations SET status = "annulée" WHERE reservation_id = :reservation_id');
                $query->bindParam(':reservation_id', $reservation_id);
                return $query->execute();
            } else {
                return false; 
            }
        }
        return false;
    }

    // 6. Récupérer le nombre actuel de réservations pour un camping
    public function getCurrentReservations($campsite_id) {
        $query = $this->pdo->prepare('SELECT COUNT(*) FROM reservations WHERE campsite_id = :campsite_id AND status != "annulée"');
        $query->bindParam(':campsite_id', $campsite_id);
        $query->execute();
        return $query->fetchColumn(); 
    }

    public function deleteReservationById($reservation_id){
        $sql = 'DELETE FROM reservations WHERE reservation_id = ?';
        $stmt =  $this->pdo->prepare($sql);
        $stmt->execute ([$reservation_id]);
    }
}
