<?php

$base = 'http://localhost/devsbook_oo';

$db_name = 'devsbook_oo';
$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';

$pdo = new PDO("mysql:dbname=".$db_name.";host=".$db_host, $db_user, $db_pass);