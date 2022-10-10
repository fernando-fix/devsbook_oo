<?php
class User {
    public $id;
    public $email;
    public $password;
    public $name;
    public $birthdate;
    public $city;
    public $work;
    public $avatar = 'default';
    public $cover = 'default';
    public $token;
}

interface UserDao {
    public function findByToken($token);
    public function findByEmail($email);
    public function findById($id);
    public function findByName($name);
    public function update(User $u);
    public function insert(User $u);
}