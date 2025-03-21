<?php
class Model
{
    public $pdo; // C'est un connecteur entre l'interface et la base de données
    public $table;

    public function __construct($table)
    {
        $this->table = $table;
        try {
            $this->pdo = new PDO("mysql:host=localhost;dbname=nationalpark;charset=utf8", "root", "");

        } catch (Exception $e) {
            die('Erreur : ' . $e->getMessage());
        }
    }
}

