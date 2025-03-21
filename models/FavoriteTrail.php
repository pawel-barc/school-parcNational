<?php
require_once 'Model.php';
// Constructeur de la classe, hérite de la classe parent Model
class FavoriteTrail extends Model{
    public function __construct($table){
        parent::__construct($table);
    }

    // Méthode pour récupérer une "trail" favorite spécifique pour un utilisateur
    public function getFavoriteTrail($trailId){
        $sql = 'SELECT * FROM favorites_trails WHERE user_id = ? AND trail_id = ?';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$_SESSION['user_id'], $trailId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Méthode pour vérifier si une "trail" est favorite pour un utilisateur
    public function isFavorite($userId, $trailId) {
        $sql = 'SELECT COUNT(*) FROM favorites_trails WHERE user_id = ? AND trail_id = ?';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$userId, $trailId]);
        return $stmt->fetchColumn() > 0; 
    }

    // Méthode pour ajouter une "trail" aux favoris d'un utilisateur
    public function addFavoriteTrail($trailId){
        if (!$this->isFavorite($_SESSION['user_id'], $trailId)) {
        $sql = 'INSERT INTO favorites_trails(user_id, trail_id) VALUES(?,?)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$_SESSION['user_id'], $trailId]);
    }
}
    // Méthode pour supprimer une "trail" des favoris d'un utilisateur
    public function deleteFavoriteTrail($trailId){
        $sql = 'DELETE FROM favorites_trails WHERE user_id = ? AND trail_id = ?';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$_SESSION['user_id'], $trailId]);
    }

    // Méthode pour récupérer toutes les "trails" favorites pour un utilisateur spécifique
    public function getFavoriteTrailByUser(){
        $sql = 'SELECT t.trail_id, t.image FROM favorites_trails f
        JOIN trails t ON f.trail_id = t.trail_id 
        WHERE f.user_id = ?';
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

}