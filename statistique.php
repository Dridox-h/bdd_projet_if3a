<?php
// Inclure le fichier de connexion à la base de données
include 'db_connect.php';

// Requête pour récupérer le nombre total d'adhérents
$sql_total_adherents = "SELECT COUNT(*) AS total_adherents FROM utilisateur";
$stmt_total_adherents = $conn->prepare($sql_total_adherents);
$stmt_total_adherents->execute();
$total_adherents = $stmt_total_adherents->fetch(PDO::FETCH_ASSOC)['total_adherents'];

// Requête pour récupérer le nombre total d'heures réservées sur l'année par adhérent
$sql_heures_par_adherent = "SELECT ut.nom, SUM(TIMESTAMPDIFF(HOUR, start_datetime, end_datetime)) AS heures_reservees
                            FROM reservation
                            INNER JOIN inscrits i ON i.id_reservation = reservation.id_reservation
                            INNER JOIN utilisateur ut ON ut.id_user = i.id_user
                            GROUP BY ut.nom";
$stmt_heures_par_adherent = $conn->prepare($sql_heures_par_adherent);
$stmt_heures_par_adherent->execute();
$heures_par_adherent = $stmt_heures_par_adherent->fetchAll(PDO::FETCH_ASSOC);

// Requête pour récupérer le taux de réservation moyen de chaque court par semaine
$sql_taux_reservation_court = "SELECT c.id_court, c.emplacement, AVG(TIMESTAMPDIFF(HOUR, start_datetime, end_datetime)) AS taux_reservation
                                FROM courts c
                                INNER JOIN reservation r ON c.id_court = r.id_court
                                GROUP BY c.id_court";
$stmt_taux_reservation_court = $conn->prepare($sql_taux_reservation_court);
$stmt_taux_reservation_court->execute();
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
    <h1>Statistiques</h1>

    <h2>Nombre total d'adhérents : <?php echo $total_adherents; ?></h2>

    <h2>Nombre d'heures réservées sur l'année par adhérent :</h2>
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
            <td><?php if(isset($row['id_user'])) echo $row['id_user']; ?></td>
            <td><?php echo $row['heures_reservees']; ?></td>
        </tr>
    <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Taux de réservation moyen de chaque court par semaine :</h2>
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
