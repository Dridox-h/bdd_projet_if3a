<?php
// Inclure le fichier de connexion à la base de données
include 'db_connect.php';
session_start(); 

$id_user = $_SESSION['id_user'];

// Requête pour récupérer les identifiants et noms des clubs de l'utilisateur
$sql_clubs = "SELECT club.id_club, club.nom_club FROM club
              INNER JOIN appartenance_club ac ON ac.id_club = club.id_club
              INNER JOIN utilisateur ut ON ut.id_user = ac.id_user
              WHERE ut.id_user = ?";
$stmt_clubs = $conn->prepare($sql_clubs);
$stmt_clubs->execute([$id_user]);    
$clubs = $stmt_clubs->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Statistiques par Club</title>
    <link href="stylesheet/styles.css" rel="stylesheet">
    <style>
        table {
            border-collapse: collapse;
            width: 50%;
            margin: 20px auto;
        }

        th, td {
            border: 1px solid #dddddd;
            padding: 8px;
            text-align: left;
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
    <h1>Statistiques par Club</h1>

    <?php foreach ($clubs as $club): ?>
        <?php 
        $club_id = $club['id_club'];
        $club_name = $club['nom_club'];

        // Requête pour récupérer le nombre total d'adhérents dans le club
        $sql_total_adherents = "SELECT COUNT(*) AS total_adherents FROM utilisateur
                                INNER JOIN appartenance_club ac ON utilisateur.id_user = ac.id_user
                                WHERE ac.id_club = ?";
        $stmt_total_adherents = $conn->prepare($sql_total_adherents);
        $stmt_total_adherents->execute([$club_id]);
        $total_adherents = $stmt_total_adherents->fetch(PDO::FETCH_ASSOC)['total_adherents'];

        // Requête pour récupérer le nombre total d'heures réservées sur l'année par adhérent dans le club
        $sql_heures_par_adherent = "SELECT utilisateur.nom, SUM(TIMESTAMPDIFF(HOUR, reservation.start_datetime, reservation.end_datetime)) AS heures_reservees
                                    FROM reservation
                                    INNER JOIN inscrits ON reservation.id_reservation = inscrits.id_reservation
                                    INNER JOIN utilisateur ON inscrits.id_user = utilisateur.id_user
                                    INNER JOIN appartenance_club ON utilisateur.id_user = appartenance_club.id_user
                                    INNER JOIN club ON club.id_club= appartenance_club.id_club
                                    WHERE appartenance_club.id_club = ? AND YEAR(reservation.start_datetime) = YEAR(CURDATE()) AND club.id_club=?
                                    GROUP BY utilisateur.nom";
        $stmt_heures_par_adherent = $conn->prepare($sql_heures_par_adherent);
        $stmt_heures_par_adherent->execute([$club_id,$club_id]);
        $heures_par_adherent = $stmt_heures_par_adherent->fetchAll(PDO::FETCH_ASSOC);


        // Requête pour récupérer le taux de réservation moyen de chaque court par semaine dans le club
        $sql_taux_reservation_court = "SELECT courts.emplacement, AVG(TIMESTAMPDIFF(HOUR, reservation.start_datetime, reservation.end_datetime)) AS taux_reservation
                                        FROM courts
                                        INNER JOIN reservation ON courts.id_court = reservation.id_court
                                        INNER JOIN appartenance_club ON courts.id_club = appartenance_club.id_club
                                        WHERE appartenance_club.id_club = ?
                                        GROUP BY courts.emplacement";
        $stmt_taux_reservation_court = $conn->prepare($sql_taux_reservation_court);
        $stmt_taux_reservation_court->execute([$club_id]);
        $taux_reservation_court = $stmt_taux_reservation_court->fetchAll(PDO::FETCH_ASSOC);
        ?>

        <h2>Statistiques du Club "<?php echo $club_name; ?>"</h2>

        <h3>Nombre total d'adhérents dans le Club : <?php echo $total_adherents; ?></h3>

        <h3>Nombre d'heures réservées sur l'année par adhérent dans le Club :</h3>
        <table>
            <thead>
                <tr>
                    <th>Adhérent</th>
                    <th>Heures réservées</th>
                </tr>
            </thead>
            <tbody><!-- on affiche les différentes heures par adhérent -->
            <?php foreach ($heures_par_adherent as $row): ?>
            <tr>
                <td><?php echo $row['nom']; ?></td>
                <td><?php echo $row['heures_reservees']; ?></td>
            </tr>
        <?php endforeach; ?>
            </tbody>
        </table>

        <h3>Taux de réservation moyen de chaque court par semaine dans le Club :</h3>
        <table>
            <thead>
                <tr>
                    <th>Court</th>
                    <th>Taux de réservation moyen (heures)</th>
                </tr>
            </thead>
            <tbody> <!-- on affiche les différents taux de réservation-->
                <?php foreach ($taux_reservation_court as $row): ?>
                    <tr>
                        <td><?php echo $row['emplacement']; ?></td>
                        <td><?php echo $row['taux_reservation']; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php endforeach; ?>
</body>
</html>
