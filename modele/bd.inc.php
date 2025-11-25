<?php

function connexionPDO() {
    $login = 'root';
    $mdp = '';
    $bd = 'gsbv0v3';
    $serveur = 'localhost';

    try {
        $conn = new PDO("mysql:host=$serveur;port=3307;dbname=$bd",$login,$mdp, array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\'')); 
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch (PDOException $e) {
        print("Erreur de connexion PDO :");
		print($e);
        die();
    }
}
