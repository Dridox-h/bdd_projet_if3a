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

print_r($date_debut);
print_r($date_fin);

$parsedDate = date_parse($date_debut);
print_r($parsedDate); 
//echo $date_debut,$date_fin,$nomClub,$terrain;
// Valider les données d'entrée
if (empty($date_debut) || empty($date_fin) || empty($nomClub)|| empty($terrain)) {
    echo "Tous les champs sont requis.";
    exit();
}

// Convertir les chaînes de date en objets DateTime
$date_debut_obj = new DateTimeImmutable($date_debut);
$date_fin_obj = new DateTimeImmutable($date_fin);

// Vérifier si la conversion a échoué
if ($date_debut_obj === false || $date_fin_obj === false) {
    echo "Erreur lors de la conversion des dates en objets DateTime";
    exit();
}

// Calculer la différence entre les dates
$diff = $date_fin_obj->diff($date_debut_obj);

if ($diff->h >2){
    header("Location: index.php?erreur=date_invalid_supérieur_à_2h");
    exit();

}
// Afficher la différence
echo "Différence: " . $diff->format('%a jours, %h heures et %i minutes');

// Insérer les données dans la table de réservation
$sql = "SELECT id_court FROM courts WHERE emplacement = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$terrain]);
$id_court = $stmt->fetchColumn();


$sql = "INSERT INTO reservation (start_datetime, end_datetime, id_court) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);

// Exécuter la requête
$result = $stmt->execute([$date_debut,$date_fin,$id_court]);



// Vérifier si l'insertion a réussi
if ($result) {
    echo "Réservation effectuée avec succès !";
    $sql = "SELECT id_reservation FROM reservation WHERE start_datetime = ? AND end_datetime = ? AND id_court = ?";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$date_debut, $date_fin, $id_court]);
    $id_reservation_row = $stmt->fetch(); // Utilisez fetch() pour obtenir une seule ligne
    if ($id_reservation_row) {
        $id_reservation = $id_reservation_row['id_reservation'];
        // Utilisez $id_reservation comme nécessaire dans votre application
        echo "ID de réservation: " . $id_reservation;
        $_SESSION['id_reservation'] = $id_reservation;

    } else {
        echo "Aucune réservation trouvée avec ces détails.";
    }
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
    <h2>Choississez invité si vous voulez jouer avec un invite</h2>
    <form action="ajout_inscrit_code.php"  method="post">
    <?php $sql = "SELECT * FROM utilisateur";
    $stmt = $conn->prepare($sql);
    $stmt->execute();   
    $joueurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Créer le menu déroulant des terrains

    echo '<select id="joueur1" name="joueur1" >';
    echo '<option value=""></option>'; // Option null
    foreach ($joueurs as $joueur) {
        // Utiliser l'id du court et l'emplacement pour l'option
        echo '<option value="' . htmlspecialchars($joueur['nom'], ENT_QUOTES, 'UTF-8') . '">' .
            htmlspecialchars($joueur['nom'], ENT_QUOTES, 'UTF-8') .
            '</option>';
    }
    echo '</select>';
    
    echo '<select id="joueur2" name="joueur2" >';
    echo '<option value=""></option>'; // Option null
    foreach ($joueurs as $joueur) {
        // Utiliser l'id du court et l'emplacement pour l'option
        echo '<option value="' . htmlspecialchars($joueur['nom'], ENT_QUOTES, 'UTF-8') . '">' .
            htmlspecialchars($joueur['nom'], ENT_QUOTES, 'UTF-8') .
            '</option>';
    }
    echo '</select>';
    
    echo '<select id="joueur3" name="joueur3" >';
    echo '<option value=""></option>'; // Option null
    foreach ($joueurs as $joueur) {
        // Utiliser l'id du court et l'emplacement pour l'option
        echo '<option value="' . htmlspecialchars($joueur['nom'], ENT_QUOTES, 'UTF-8') . '">' .
            htmlspecialchars($joueur['nom'], ENT_QUOTES, 'UTF-8') .
            '</option>';
    }
    echo '</select>';
?>
    <input type="submit" value="Envoyer"><br>
</form>

</body>
</html>