<?php

$host = 'localhost';
$dbh   = 'airbnb';   
$user = 'root';       
$pass = '';           

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbh;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion : " . $e->getMessage());
}

?>
