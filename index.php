<?php
try {
    $bdd = new PDO("mysql:host=localhost;dbname=tennis;charset=utf8", "root", "");
} catch (PDOException $e) {
    die('Erreur de connexion : ' . $e->getMessage());
}

// Commencez la session pour utiliser $_SESSION
session_start();

// Vérifiez si l'utilisateur est connecté
$is_logged_in = isset($_SESSION['id_user']);

// Requête pour récupérer les réservations
$query = 'SELECT * FROM reservation';
$stmt = $bdd->query($query);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Tableau des jours de la semaine
$jours = ["Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi"];

// Affichage de l'emploi du temps
echo "<div class='container'><table class='table table-bordered' style='text-align:center;' border='1'>";
echo "<tr><th>Heure</th>";
foreach ($jours as $jour) {
    echo "<th>{$jour}</th>";
}
echo "</tr>";

// Boucle sur les heures de 8h à 18h
for ($heure = 8; $heure <= 18; $heure++) {
    echo "<tr>";
    echo "<td style='background:#CCCCCC; font-weight: bold;'>{$heure}h00</td>";

    // Boucle sur les jours
    for ($d = 0; $d < 5; $d++) {
        $date_num = date("Y-m-d", mktime(0, 0, 0, 4, 22 + $d, 2024)); // Adaptez la date selon vos besoins
        echo "<td style='padding:10px; border:1px solid #999999;'>";

        // Boucle sur les réservations pour vérifier les réservations à la date et à l'heure données
        $reservation_found = false;
        foreach ($reservations as $reservation) {
            echo $heure;
            echo "</br>";
            echo (int)date("H", strtotime($reservation['heure_debut']));
            
            if ($date_num == $reservation['date_reservation'] && $heure == (int)date("H", strtotime($reservation['heure_debut']))) {
                $heure_debut = new DateTime($reservation['heure_debut']);
                $heure_fin = clone $heure_debut;
                $heure_fin->add(new DateInterval('PT' . $reservation['duree'] . 'H'));
                

                // Afficher la réservation si elle correspond à la plage horaire actuelle
                if ($heure_debut->format('H:i') == "{$heure}:00") {
                    echo "Réservation sur le court {$reservation['id_court']}";
                    $reservation_found = true;
                    break;
                }
            }
        }

        // Si aucune réservation n'est trouvée, afficher "Disponible"
        if (!$reservation_found) {
            echo "Disponible";
        }

        echo "</td>";
    }

    echo "</tr>";
}
echo "</table></div>";
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Réservation de court de Tennis</title>
    <link rel="stylesheet" href="./stylesheet/styles.css">

</head>
<body>
    <?php if (isset($_SESSION['id_user'])) : ?>
        <h1>Bienvenue sur le site de réservation de courts de tennis !</h1>
    <?php else : ?>
        <h1>Connexion</h1>
        <form class="form-container" action="connexion.php" method="post">
            <label for="email">Email : </label>
            <input type="email" name="email" id="email" required>
            <label for="password">Mot de passe</label>
            <input type="password" name="password" id="password" required>
            <input type="submit" name="submit" value="Connexion">
            Vous n'êtes pas encore inscrit ? <a href="inscription.php">S'inscrire </a>
        </form>
    <?php endif; ?>
</body>
</html>


