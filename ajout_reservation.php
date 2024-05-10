<?php
include 'db_connect.php';

session_start(); 


$userId = $_SESSION['id_user'];

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
    <h1>Bienvenue sur l'agenda des clubs ! </h1></br>


    <h2>Prendre une nouvelle réservation : </h2>
    <form action="ajout_reservation_code.php"  method="post">
    <label for="nom">start date : </label>
    <input type="datetime" id="start_date" name="start_date" required placeholder="YYYY-MM-DDTHH:MM:SS">


    <label for="duree">end date : </label>
    <input type="datetime" id="end_date" name="end_date"  required placeholder="YYYY-MM-DDTHH:MM:SS">

        <?php
        // Récupérer le nom du club depuis l'URL

// Afficher le nom du club sélectionné

// Préparer la requête SQL pour récupérer les terrains associés au club sélectionné
$sql = "SELECT c.nom_club,cr.emplacement FROM utilisateur INNER JOIN appartenance_club ac ON ac.id_user = utilisateur.id_user INNER JOIN club c ON c.id_club=ac.id_club INNER JOIN courts cr ON cr.id_club=c.id_club WHERE utilisateur.id_user = ?";
// Préparer et exécuter la requête avec le nom du club
$stmt = $conn->prepare($sql);
$stmt->execute([$userId]);
// Récupérer les résultats de la requête
$clubs = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Créer le menu déroulant des terrains

echo '<select id="nom_club" name="nom_club">';
foreach ($clubs as $club) {
    // Utiliser l'id du court et l'emplacement pour l'option
    echo '<option value="' . htmlspecialchars($club['nom_club'], ENT_QUOTES, 'UTF-8') . '">' .
        htmlspecialchars($club['nom_club'], ENT_QUOTES, 'UTF-8') .
        '</option>';
}
echo '</select>';


echo '<select id="terrain" name="terrain">';
foreach ($clubs as $club) {
    // Utiliser l'id du court et l'emplacement pour l'option
    echo '<option value="' . htmlspecialchars($club['emplacement'], ENT_QUOTES, 'UTF-8') . '">' .
        htmlspecialchars($club['emplacement'], ENT_QUOTES, 'UTF-8') .
        '</option>';
}
echo '</select>';

?>
    <input type="submit" value="Envoyer"><br>
    <a href="index.php">CLiquer ici pour retourner à l'agenda</a>

    </form>
    </div>
</body>
</html>