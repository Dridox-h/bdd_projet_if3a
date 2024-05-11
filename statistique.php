<?php
// Inclure le fichier de connexion à la base de données
include 'db_connect.php';
session_start(); 

$id_user = $_SESSION['id_user'];

// Requête pour récupérer l'identifiant et le nom du club de l'utilisateur
$sql_club_info = "SELECT club.id_club, club.nom_club FROM club
                INNER JOIN appartenance_club ac ON ac.id_club = club.id_club
                INNER JOIN utilisateur ut ON ut.id_user = ac.id_user
                WHERE ut.id_user = ?";
$stmt_club_info = $conn->prepare($sql_club_info);
$stmt_club_info->execute([$id_user]);    
$club_info = $stmt_club_info->fetch(PDO::FETCH_ASSOC);

$club_id = $club_info['id_club'];
$club_name = $club_info['nom_club'];

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
                            WHERE appartenance_club.id_club = ?
                            GROUP BY utilisateur.nom";
$stmt_heures_par_adherent = $conn->prepare($sql_heures_par_adherent);
$stmt_heures_par_adherent->execute([$club_id]);
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

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Statistiques</title>
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
    <h1>Statistiques du Club "<?php echo $club_name; ?>"</h1>

    <h2>Nombre total d'adhérents dans le Club : <?php echo $total_adherents; ?></h2>

    <h2>Nombre d'heures réservées sur l'année par adhérent dans le Club :</h2>
    <table>
        <thead>
            <tr>
                <th>Adhérent</th>
                <th>Heures réservées</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($heures_par_adherent as $row): ?>
        <tr>
            <td><?php echo $row['nom']; ?></td>
            <td><?php echo $row['heures_reservees']; ?></td>
        </tr>
    <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Taux de réservation moyen de chaque court par semaine dans le Club :</h2>
    <table>
        <thead>
            <tr>
                <th>Court</th>
                <th>Taux de réservation moyen (heures)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($taux_reservation_court as $row): ?>
                <tr>
                    <td><?php echo $row['emplacement']; ?></td>
                    <td><?php echo $row['taux_reservation']; ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
