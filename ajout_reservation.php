<?php
include 'db_connect.php';

session_start(); 

$userId = $_SESSION['id_user'];

if (isset($_POST['supp_reservation'])) {
    $id_res = $_POST['supp_reservation'];
    echo $id_res;

    $req = $bdd->prepare("DELETE FROM inscrits WHERE id_reservation = ?");
    $req->execute([$id_res]);
    $req = $bdd->prepare("DELETE FROM reservation WHERE id_reservation = ?");
    $req->execute([$id_res]);
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Event Calendar</title>
    <link href="stylesheet/styles.css" rel="stylesheet" type="text/css">
    <link href="stylesheet/calendar.css" rel="stylesheet" type="text/css">

    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th, td {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <div id ="MenuBarre">
        <a href="index.php">Page d'accueil</a>
    </div>
    <h1>Bienvenue sur l'agenda des clubs ! </h1></br>


    <h2>Prendre une nouvelle réservation : </h2>

    <form action="ajout_reservation_code.php"  method="post">
    <label for="nom">start date : </label>
    <input type="datetimel" id="start_date" name="start_date"  placeholder="YYYY-MM-DD HH:MM:SS">


    <label for="duree">end date : </label>
    <input type="datetime" id="end_date" name="end_date"   placeholder="YYYY-MM-DD HH:MM:SS">

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

        // Vérifier le nombre de réservations de l'utilisateur
        $sql_count_reservations = "SELECT COUNT(*) AS count FROM inscrits WHERE id_user = ? AND inscrits.role = 'leader'";
        $stmt_count_reservations = $conn->prepare($sql_count_reservations);
        $stmt_count_reservations->execute([$userId]);
        $row_count_reservations = $stmt_count_reservations->fetch(PDO::FETCH_ASSOC);
        $num_reservations = $row_count_reservations['count'];

        //echo $num_reservations;

        if ($num_reservations >= 1) {
            echo "Nombre maximal de réservations atteint ! Maximum : 1 par personne<br>";
            echo '<h1><a href="index.php">menu</a></h1>';
        }
        ?>

        <?php if ($num_reservations != 1){
            echo "<input type='submit' value='Envoyer'><br>";
        }
        ?>
        </form>
    <h2>Supprimer une réservation</h2>

    <table>
        <thead>
            <th> Reservation</th>
            <th> supprimer</th>
        </thead>
        <tbody>
            <th>
                <?php 
                $sql = "SELECT * FROM inscrits INNER JOIN utilisateur ut ON ut.id_user=inscrits.id_user INNER JOIN reservation r ON r.id_reservation = inscrits.id_reservation WHERE ut.id_user = ? AND inscrits.role = 'leader'";
                $stmt3 = $conn->prepare($sql);
                $stmt3->execute([$userId]);
                $reservation = $stmt3->fetch(PDO::FETCH_ASSOC);

                if (!empty($reservation)){
                    //print_r($reservation);
                    echo "voici votre reservation en tant que leader :  <br>"; 
                    echo ($reservation["role"]." qui commence le ".$reservation["start_datetime"]." et finit le ". $reservation["end_datetime"]);
                }
                ?>
            </th>
            <th>
                <form action="suppression.php" method="POST" style="display:inline;">
                    <input type="hidden" name="supp_reservation" value="<?php echo $reservation['id_reservation']; ?>">
                    <button type="submit" name="submit_delete" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réservation ?')">Supprimer</button>
                </form>
            </th>
        </tbody>
    </table>
</body>
</html>
