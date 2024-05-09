<?php
include 'db_connect.php';

// Vérifier si la requête est POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Utiliser isset() pour vérifier si les clés existent dans $_POST
    if (isset($_POST['date']) && isset($_POST['duree']) && isset($_POST['heure_debut']) && isset($_POST['terrain'])) {
        // Récupérer les données soumises
        $date = $_POST['date'];
        $duree = $_POST['duree'];
        $heure_debut = $_POST['heure_debut'];
        $terrain = $_POST['terrain'];
        echo $terrain;
        

        try {
            // Préparer la requête d'insertion
            $sql = "INSERT INTO reservations (id_reservation,date_reservation, heure_debut, duree, id_court) VALUES (10,:date, :heure_debut, :duree, :terrain)";
            
            // Préparer la requête
            $stmt = $conn->prepare($sql);
            
            // Lier les paramètres à la requête préparée
            $stmt->bindParam(':date', $date);
            $stmt->bindParam(':duree', $duree);
            $stmt->bindParam(':heure_debut', $heure_debut);
            $stmt->bindParam(':terrain', $terrain);

            // Exécuter la requête préparée
            if ($stmt->execute()) {
                echo "Réservation ajoutée avec succès !";
            } else {
                echo "Erreur lors de l'ajout de la réservation.";
            }
        } catch (PDOException $e) {
            // Gérer les erreurs liées à la base de données
            echo "Erreur de base de données : " . $e->getMessage();
        }
    }
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
    <h1>Bienvenue sur l'agenda des clubs ! </h1></br>


    <h2>Prendre une nouvelle réservation : </h2>
    <label for="nom">date :</label>
    <input type="date" id="date" name="date" required>


    <label for="duree">Durée en HH:MM:SS :</label>
    <input type="text" id="duree" name="duree" pattern="\d{2}:\d{2}:\d{2}" required placeholder="HH:MM:SS">

    <!-- Champ d'heure de début -->
    <label for="heure_debut">Heure de début en HH:MM:SS :</label>
    <input type="time" id="heure_debut" name="heure_debut" required step="1">

    <form id="reservation" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <?php
        // Récupérer le nom du club depuis l'URL
$nom_club = isset($_GET['club']) ? $_GET['club'] : '';

// Afficher le nom du club sélectionné
echo "Le club sélectionné est : " . htmlspecialchars($nom_club, ENT_QUOTES, 'UTF-8');

// Préparer la requête SQL pour récupérer les terrains associés au club sélectionné
$sql = "SELECT courts.id_court, courts.emplacement FROM courts INNER JOIN club ON club.id_club = courts.id_club WHERE club.nom_club = ?";

// Préparer et exécuter la requête avec le nom du club
$stmt = $conn->prepare($sql);
$stmt->execute([$nom_club]);

// Récupérer les résultats de la requête
$terrains = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Créer le menu déroulant des terrains
echo '<select id="terrain" name="terrain">';
foreach ($terrains as $terrain) {
    // Utiliser l'id du court et l'emplacement pour l'option
    echo '<option value="' . htmlspecialchars($terrain["id_court"], ENT_QUOTES, 'UTF-8') . '">' .
        htmlspecialchars($terrain["emplacement"], ENT_QUOTES, 'UTF-8') .
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