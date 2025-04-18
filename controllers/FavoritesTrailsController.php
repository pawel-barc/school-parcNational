<?php

require_once 'Controller.php';
require_once __DIR__ . '/../models/FavoriteTrail.php';

class FavoritesTrailsController extends Controller{
    // Méthode pour gérer l'ajout ou la suppression d'un sentier favori
    public function manageFavoriteTrail() {
        if (isset($_SESSION['user_id'])){
            $favoriteTrailObject = new FavoriteTrail('favorites_trails');
            $trailId = $_GET['trail_id'];
            $favoriteTrail = $favoriteTrailObject->getFavoriteTrail($trailId);
            if ($favoriteTrail === false) {
                $favoriteTrailObject->addFavoriteTrail($trailId);
                $this->redirect('trails');
            } else {
                $favoriteTrailObject->deleteFavoriteTrail($trailId);
                $this->redirect('profile');
            }
        }else{
            $this->redirect('login');
        }
    }
    
    // Méthode AJAX pour gérer l'ajout ou la suppression d'un sentier favori
    public function manageFavoriteTrailAjax() {
        if (isset($_SESSION['user_id'])){
            $favoriteTrailObject = new FavoriteTrail('favorites_trails');
            $trailId = $_GET['trail_id'];
            $favoriteTrail = $favoriteTrailObject->getFavoriteTrail($trailId);
            if ($favoriteTrail === false) {
                $favoriteTrailObject->addFavoriteTrail($trailId);
            } else {
                $favoriteTrailObject->deleteFavoriteTrail($trailId);
            }
        }else{
            $this->redirect('login');
        }

    }

}