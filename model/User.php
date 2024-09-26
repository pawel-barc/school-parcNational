<?php

require_once 'Model.php';

class User extends Model{
    public function __construct($table){
        parent::__construct($table);
    }
    public function saveUser($data){
        //Database keys
        $sql = 'INSERT INTO users (role, lastname, firstname, mail, password, phone, address, city, zipcode) values (?,?,?,?,?,?,?,?,?)';
        $stmt = $this->pdo->prepare($sql);
        //"names" from Form
        $stmt->execute([1, $data['lastname'], $data['firstname'], $data['email'], password_hash($data['password'], PASSWORD_BCRYPT), $data['phone'], $data['adress'], $data['city'], $data['zipcode']]);
    }
    public function getUserByEmail($email){
        $sql = ' SELECT * FROM users WHERE mail = ?';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    public function saveUserFromGoogle($data){
        var_dump($data);
        $sql = ' INSERT INTO users (role, lastname, firstname, mail, google_id) values (?,?,?,?,?)';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([1, $data->family_name, $data->given_name, $data->email, $data->sub]);//names from google

    }
    public function getByGoogleId($googleId){
        $sql = 'SELECT * FROM users WHERE google_id = ?';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$googleId]);
        return $stmt->fetch();
    }

}
