<?php
require 'config.php';
require 'models/Auth.php';

$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$password = filter_input(INPUT_POST, 'password');

if($mail && $password) {

    $auth = new Auth($pdo, $base);

    if($auth->validateLogin($email, $password)) {
         header("Location: ".$base);
         exit;
    }

}

$_SESSION['flash'] = "Login e/ou senha incorreto(s)!"; 
header('Location: '.$base);
exit;