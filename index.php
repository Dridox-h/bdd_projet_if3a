<?php
include 'Calendar.php';
include 'db_connect.php';

// Initialisation du calendrier
$calendar = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Si le formulaire est soumis, vérifiez si un club a été sélectionné
    if (isset($_POST['liste_club']) && !empty($_POST['liste_club'])) {
        // Vous pouvez récupérer le nom du club sélectionné dans $_POST['liste_club']
        $selectedClub = $_POST['liste_club'];

        // Vous pouvez initialiser la date du calendrier selon vos besoins
        $calendar = new Calendar('2024-05-12');

        // Récupérez les événements liés au club sélectionné
        $sqlUpdateEvent = "SELECT * FROM reservation WHERE id_court = (SELECT id_court FROM courts INNER JOIN club ON club.id_club=courts.id_club  WHERE nom_club = ?)";
        $stmt = $conn->prepare($sqlUpdateEvent);
        $stmt->execute([$selectedClub]);
        $events = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($events as $event) {
            $sqlterrain = "SELECT emplacement FROM courts 
                           INNER JOIN reservation ON courts.id_court = reservation.id_court 
                           WHERE reservation.id_court = ?";
            $stmt2 = $conn->prepare($sqlterrain);
            $stmt2->execute([$event["id_court"]]);
            $terrain = $stmt2->fetch(PDO::FETCH_ASSOC);

            // Assurez-vous de vérifier si l'emplacement existe dans $terrain
            if ($terrain && isset($terrain["emplacement"])) {
                $calendar->add_event(
                    "L'événement commence à " . $event["heure_debut"] . " et dure : " . $event["duree"] . " sur le terrain : " . $terrain["emplacement"],
                    $event['date_reservation'],
                    1,
                    'green'
                );
            }
        }
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Réservation de court de Tennis</title>
    <link rel="stylesheet" href="./stylesheet/styles.css">
    <link href="stylesheet/calendar.css" rel="stylesheet" type="text/css">

</head>
<body>
<div id=MenuBarre>
    <h3>
        <?php if (isset($_SESSION['id_user'])) :
            echo $_SESSION['id_user']?>
            Connecté en tant que :
            <?php
            $req = $bdd->prepare("SELECT nom,prenom FROM utilisateur WHERE id_user = ?");
            $req->execute([$_SESSION['id_user']]);
            $donnees = $req->fetch();
            echo $donnees['nom'], " ", $donnees['prenom'];
            ?>
            <br/>
            <a href="deconnexion.php">Déconnexion</a>
        <?php else : ?>
            <a href="connexion.php" >Connexion</a>
        <?php endif; ?>
    </h3>
</div>
<h1>Bienvenue sur l'agenda des clubs ! </h1></br>
<h2>Veuiller choisir quel club vous voulez visualisez : </h2></br>


<form id="clubForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
    <?php
    // Récupérez la liste des clubs pour créer le menu déroulant
    $sqlUpdateEvent = "SELECT * FROM club";
    $stmt = $conn->prepare($sqlUpdateEvent);
    $stmt->execute();
    $clubs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo '<select id="liste_club" name="liste_club">';
    foreach ($clubs as $club) {
        echo '<option value="' . htmlspecialchars($club["nom_club"], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($club["nom_club"], ENT_QUOTES, 'UTF-8') . '</option>';
    }
    echo '</select>';

    // Ajoutez le bouton d'envoi
    echo '<button type="submit">Envoyer</button>';


    ?>
</form></body><br>

<input type="hidden" id="selected_club" name="selected_club" value="<?php echo isset($_POST['liste_club']) ? $_POST['liste_club'] : ''; ?>">
<a href="ajout_reservation.php?club=<?php echo isset($_POST['liste_club']) ? urlencode($_POST['liste_club']) : ''; ?>">Cliquez ici pour ajouter une réservation</a>

<nav class="navtop">
    <div>
        <h1>Event Calendar</h1>
    </div>
</nav>
<div class="content home">
    <?php
    // Affichez le calendrier uniquement si un club a été sélectionné et que le formulaire a été soumis
    if ($calendar) {
        echo $calendar;
    }
    ?>
</div>
</body>
</html>


