<?php
include 'db_connect.php'; // Inclure la connexion à la base de données

session_start(); // Démarrer la session

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['id_user'])) {
    header('Location: login.php');
    exit();
}

$userId = $_SESSION['id_user'];

// Vérifier que la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Requête invalide.";
    exit();
}

// Récupérer les données soumises
$date_debut = htmlspecialchars($_POST['start_date'], ENT_QUOTES, 'UTF-8');
$date_fin = htmlspecialchars($_POST['end_date'], ENT_QUOTES, 'UTF-8');
$nomClub = htmlspecialchars($_POST['nom_club'], ENT_QUOTES, 'UTF-8');
$terrain = htmlspecialchars($_POST['terrain'], ENT_QUOTES, 'UTF-8');

echo $date_debut,$date_fin,$nomClub,$terrain;
// Valider les données d'entrée
if (empty($date_debut) || empty($date_fin) || empty($nomClub)|| empty($terrain)) {
    echo "Tous les champs sont requis.";
    exit();
}

// Insérer les données dans la table de réservation
$sql = "SELECT id_court FROM courts WHERE emplacement = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$terrain]);
$id_court = $stmt->fetchColumn();

echo $id_court;

$sql = "INSERT INTO reservation (start_datetime, end_datetime, id_court) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);

// Exécuter la requête
$result = $stmt->execute([$date_debut,$date_fin,$id_court]);



// Vérifier si l'insertion a réussi
if ($result) {
    echo "Réservation effectuée avec succès !";

} else {
    echo "Échec de l'ajout de la réservation.";
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Event Calendar</title>
    <link href="stylesheet/style.css" rel="stylesheet" type="text/css">
    <link href="stylesheet/calendar.css" rel="stylesheet" type="text/css">
</head>
<body>
    <h1>Veuillez indiquer vos partenaires de jeux 3 maximun ! </h1>

</body>
</html>