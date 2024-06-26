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


$parsedDate = date_parse($date_debut);

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
//echo "Différence: " . $diff->format('%a jours, %h heures et %i minutes');

// Insérer les données dans la table de réservation
$sql = "SELECT id_court FROM courts WHERE emplacement = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$terrain]);
$id_court = $stmt->fetchColumn();


$sql_check_overlap = "SELECT COUNT(*) AS count FROM reservation WHERE id_court = ? AND ((start_datetime BETWEEN ? AND ?) OR (end_datetime BETWEEN ? AND ?))";
$stmt_check_overlap = $conn->prepare($sql_check_overlap);
$stmt_check_overlap->execute([$id_court, $date_debut, $date_fin, $date_debut, $date_fin]);
$row_check_overlap = $stmt_check_overlap->fetch(PDO::FETCH_ASSOC);
$num_overlap = $row_check_overlap['count'];

// Si une réservation se chevauche, informez l'utilisateur et ne pas insérez la nouvelle réservation
if ($num_overlap > 0) {
    echo "Impossible de réserver ce court pendant cette période. Veuillez choisir une autre période.";
    exit();
}
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
    $id_reservation_row = $stmt->fetch(); 
    // on récupère l'id de la réservation pour la transmettre à la prochaine page, on stocke dans la session
    if ($id_reservation_row) {
        $id_reservation = $id_reservation_row['id_reservation'];
        
        //echo "ID de réservation: " . $id_reservation;
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
    <title>Reservation</title>
    <link href="stylesheet/style.css" rel="stylesheet" type="text/css">
    <link href="stylesheet/calendar.css" rel="stylesheet" type="text/css">
</head>
<body>
    <h1>Veuillez indiquer vos partenaires de jeux 3 maximun ! </h1>
    <h2>Choississez invité si vous voulez jouer avec un invite</h2>

    <!--- form pour transmettre les données servant à la réservation--->
    <form action="ajout_inscrit_code.php"  method="post">
    <?php $sql = "SELECT * FROM utilisateur";
    $stmt = $conn->prepare($sql);
    $stmt->execute();   
    $joueurs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Créer le menu déroulant des terrains

    echo '<select id="joueur1" name="joueur1" >';
    echo '<option value=""></option>'; // Option null
    foreach ($joueurs as $joueur) {
        // on affiche les différents joueur possible un par un
        echo '<option value="' . htmlspecialchars($joueur['nom'], ENT_QUOTES, 'UTF-8') . '">' .
            htmlspecialchars($joueur['nom'], ENT_QUOTES, 'UTF-8') .
            '</option>';
    }
    echo '</select>';
        // on repète 3 fois ce code pour avoir 3 cases avec 3 joueurs
    echo '<select id="joueur2" name="joueur2" >';
    echo '<option value=""></option>';
    foreach ($joueurs as $joueur) {

        echo '<option value="' . htmlspecialchars($joueur['nom'], ENT_QUOTES, 'UTF-8') . '">' .
            htmlspecialchars($joueur['nom'], ENT_QUOTES, 'UTF-8') .
            '</option>';
    }
    echo '</select>';
    
    echo '<select id="joueur3" name="joueur3" >';
    echo '<option value=""></option>'; 
    foreach ($joueurs as $joueur) {

        echo '<option value="' . htmlspecialchars($joueur['nom'], ENT_QUOTES, 'UTF-8') . '">' .
            htmlspecialchars($joueur['nom'], ENT_QUOTES, 'UTF-8') .
            '</option>';
    }
    echo '</select>';
?>  
    <!--- boutton pour envoyer les données--->

    <input type="submit" value="Envoyer"><br>
</form>

</body>
</html>