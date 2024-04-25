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


?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Réservation de court de Tennis</title>
    <link rel="stylesheet" href="./stylesheet/styles.css">

</head>
<body>
<div id=MenuBarre>
    <h3>
    <?php if (isset($_SESSION['id_user'])) : ?>
        Connecté en tant que :
            <?php
            $req = $bdd->prepare("SELECT nom,prenom FROM utilisateur WHERE id_user = ?");
            $req->execute([$_SESSION['id_user']]);
            $donnees = $req->fetch();
            echo $donnees['nom'], " ", $donnees['prenom'];
            ?>
        <?php else : ?>
        <a href="connexion.php" >Connexion</a>
        <?php endif; ?>
    </h3>
</div>
<?php
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

<div style="font-size: 20px">
    Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam faucibus gravida enim quis cursus. Sed auctor, velit vitae maximus eleifend, arcu sem lacinia justo, a porta nisi erat eu nisl. Aenean aliquam cursus risus, vitae sodales enim vehicula eget. Duis dignissim eleifend lobortis. Morbi tellus arcu, luctus sed laoreet et, placerat id nulla. Nullam non faucibus mi. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Praesent ornare lacus accumsan ligula imperdiet eleifend. Donec ut elementum ex, eu pulvinar massa. Cras venenatis eu tortor et fringilla. Donec hendrerit nisi ut massa fermentum tincidunt. Vivamus faucibus aliquam egestas. Quisque vestibulum lacus in sem finibus iaculis. Nulla facilisi.
    <br>
    Nulla odio erat, gravida eleifend diam molestie, consequat posuere sapien. Duis fermentum vulputate arcu ut semper. Integer scelerisque accumsan orci a eleifend. Vivamus facilisis erat ut tortor facilisis tempor. Nulla tempus neque in molestie luctus. Aenean efficitur nunc non ultricies semper. Vivamus blandit vel ex in dapibus. Phasellus pretium urna sed risus scelerisque ullamcorper. Sed pretium dapibus arcu sed molestie.
    <br>
    Integer vel tempus nibh. Etiam consectetur erat mauris, eu feugiat velit laoreet eu. Donec semper ipsum vitae erat pulvinar, suscipit dignissim nunc interdum. Aliquam magna mauris, suscipit finibus eleifend ac, viverra sit amet massa. Fusce nibh quam, cursus in risus a, auctor placerat nisl. Suspendisse ut mi augue. Suspendisse tincidunt nunc in quam sodales, et imperdiet purus venenatis. Curabitur commodo tellus vitae lectus convallis, ac ultrices magna iaculis. Nullam molestie imperdiet lacus, pulvinar pulvinar nisi tristique et. Donec egestas sem in lacinia fringilla.
    <br>
    Quisque at est arcu. Vestibulum et ligula eros. Maecenas tempor at risus eget porttitor. Fusce id pharetra lacus, a fringilla ante. Sed vulputate erat vel erat malesuada bibendum. Nulla tellus nisl, congue id urna rutrum, tincidunt tincidunt massa. Morbi tincidunt erat quis arcu volutpat, at dictum arcu lobortis. Praesent pellentesque velit orci, sit amet egestas lacus aliquam nec. Integer sit amet nulla mattis ex condimentum placerat. Duis vitae nulla ornare, porta ipsum porttitor, sodales tellus. Aenean non libero risus. Cras at justo nec quam condimentum ultricies. Nulla commodo ligula sit amet pellentesque sagittis. Mauris sit amet tempus mauris. Nulla quis ultrices sem. Etiam non viverra dui.
    <br>
    Maecenas placerat accumsan feugiat. Proin faucibus justo porttitor tincidunt sagittis. Nullam gravida sem in nibh tempor commodo. Phasellus convallis accumsan enim mollis ultricies. Ut a fringilla tortor, vitae fringilla tortor. In accumsan ligula eget felis posuere rhoncus. Sed vitae ex cursus dolor ullamcorper consequat et vitae nulla.
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aliquam faucibus gravida enim quis cursus. Sed auctor, velit vitae maximus eleifend, arcu sem lacinia justo, a porta nisi erat eu nisl. Aenean aliquam cursus risus, vitae sodales enim vehicula eget. Duis dignissim eleifend lobortis. Morbi tellus arcu, luctus sed laoreet et, placerat id nulla. Nullam non faucibus mi. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Praesent ornare lacus accumsan ligula imperdiet eleifend. Donec ut elementum ex, eu pulvinar massa. Cras venenatis eu tortor et fringilla. Donec hendrerit nisi ut massa fermentum tincidunt. Vivamus faucibus aliquam egestas. Quisque vestibulum lacus in sem finibus iaculis. Nulla facilisi.
    <br>
    Nulla odio erat, gravida eleifend diam molestie, consequat posuere sapien. Duis fermentum vulputate arcu ut semper. Integer scelerisque accumsan orci a eleifend. Vivamus facilisis erat ut tortor facilisis tempor. Nulla tempus neque in molestie luctus. Aenean efficitur nunc non ultricies semper. Vivamus blandit vel ex in dapibus. Phasellus pretium urna sed risus scelerisque ullamcorper. Sed pretium dapibus arcu sed molestie.
    <br>
    Integer vel tempus nibh. Etiam consectetur erat mauris, eu feugiat velit laoreet eu. Donec semper ipsum vitae erat pulvinar, suscipit dignissim nunc interdum. Aliquam magna mauris, suscipit finibus eleifend ac, viverra sit amet massa. Fusce nibh quam, cursus in risus a, auctor placerat nisl. Suspendisse ut mi augue. Suspendisse tincidunt nunc in quam sodales, et imperdiet purus venenatis. Curabitur commodo tellus vitae lectus convallis, ac ultrices magna iaculis. Nullam molestie imperdiet lacus, pulvinar pulvinar nisi tristique et. Donec egestas sem in lacinia fringilla.
    <br>
    Quisque at est arcu. Vestibulum et ligula eros. Maecenas tempor at risus eget porttitor. Fusce id pharetra lacus, a fringilla ante. Sed vulputate erat vel erat malesuada bibendum. Nulla tellus nisl, congue id urna rutrum, tincidunt tincidunt massa. Morbi tincidunt erat quis arcu volutpat, at dictum arcu lobortis. Praesent pellentesque velit orci, sit amet egestas lacus aliquam nec. Integer sit amet nulla mattis ex condimentum placerat. Duis vitae nulla ornare, porta ipsum porttitor, sodales tellus. Aenean non libero risus. Cras at justo nec quam condimentum ultricies. Nulla commodo ligula sit amet pellentesque sagittis. Mauris sit amet tempus mauris. Nulla quis ultrices sem. Etiam non viverra dui.
    <br>
    Maecenas placerat accumsan feugiat. Proin faucibus justo porttitor tincidunt sagittis. Nullam gravida sem in nibh tempor commodo. Phasellus convallis accumsan enim mollis ultricies. Ut a fringilla tortor, vitae fringilla tortor. In accumsan ligula eget felis posuere rhoncus. Sed vitae ex cursus dolor ullamcorper consequat et vitae nulla.

</div>
</body>
</html>


