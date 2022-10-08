<?php
session_start();
date_default_timezone_set('America/Sao_Paulo');

$base = 'http://localhost/devsbook_oo';
$base = 'http://192.168.1.105/devsbook_oo'; //para teste em rede local

$db_name = 'devsbook_oo';
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';

$pdo = new PDO("mysql:dbname=".$db_name.";host=".$db_host, $db_user, $db_pass);