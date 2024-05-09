<?php
try {
    // Établir une connexion à la base de données avec PDO
    $conn = new PDO("mysql:host=localhost;dbname=tennis;charset=utf8", "root", "");
    
    // Définir le mode d'erreur de PDO pour lancer des exceptions en cas d'erreur
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Autres configurations de la base de données (si nécessaire) peuvent être ajoutées ici
    
    // Si la connexion est réussie, vous pouvez continuer à travailler avec $conn
} catch (PDOException $e) {
    // Gestion de l'erreur : Afficher un message d'erreur ou effectuer d'autres actions
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

?>