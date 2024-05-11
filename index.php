<?php

session_start(); // Démarre la session de l'utilisateur

require 'db_connect.php'; // connexion à la BDD

$today = date('Y-m-d'); // on récuppère la date d'ajourd'hui

// récupération du club séléctionnée puis formatage des données en json pour ke traiter
if (!empty($_POST['liste_club'])) { 
    $query = "SELECT * FROM reservation INNER JOIN courts c ON c.id_court=reservation.id_court 
    INNER JOIN club cl ON cl.id_club=c.id_club WHERE cl.nom_club = ?";
    
    $stmt = $conn->prepare($query);
    $stmt->execute([$_POST['liste_club']]);
    $events = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $events_json = json_encode($events);
}


?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendrier de la journée</title>

    <link href="stylesheet/styles.css" rel="stylesheet"/>
    <!--Ajout de la libraire FullCalendar pour utiliser un calendrier -->
    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css' rel='stylesheet' />
    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.print.min.css' rel='stylesheet' media='print' />
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment-with-locales.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/locale/fr.js'></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css' rel='stylesheet' />
    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.print.min.css' rel='stylesheet' media='print' />
     <!-- Début du script pour l'utilisation du calendrier, on définit les variables avec les données précédent récupérée en json -->
    <script>
        $(document).ready(function() {
            var events = <?php echo $events_json; ?>;
            
            events.forEach(function(event) {
                var start = moment(event.start_datetime);
    var end = moment(event.end_datetime);
    event.start = start.format();
    event.end = end.format();
    event.title = 'cours réservé';
    
    // Calculer la durée entre les dates de début et de fin
    var duration = moment.duration(end.diff(start));
    
    // Formater la durée en heures, minutes et secondes
    var hours = Math.floor(duration.asHours());
    var minutes = duration.minutes();
    var seconds = duration.seconds();
    
    // Créer la description en utilisant la durée calculée
    event.description = "Le cours dure " + hours + " heures, " + minutes + " minutes et " + seconds + " secondes.";
            });

            $('#calendar').fullCalendar({
                defaultView: 'agendaDay',
                defaultDate: moment(),
                editable: false,
                eventLimit: false,
                events: events,
                eventClick: function(event) {
                    alert('Description: ' + event.description);
                },
            });
            // fonction permettant le changement de vue netre journalier/ hebdomadaire et mensuelle
            function changeView(view) {
                $('#calendar').fullCalendar('changeView', view);
            }
            
            $('#daily-btn').click(function() {
                changeView('agendaDay');
            });

            $('#weekly-btn').click(function() {
                changeView('agendaWeek');
            });

            $('#monthly-btn').click(function() {
                changeView('month');
            });
        });

        
    </script>
</head>
<body>

<!-- ajout de la barre de naviagation -->
<div id=MenuBarre>
    <h3> 
        <!-- La barre va afficher si l'utilisateur est connecté et lui indiquer quel page il peut utiliser sinon elle demandera de se connecter -->
        <?php if (isset($_SESSION['id_user'])) :?>
            Connecté en tant que :
            <?php
            $req = $conn->prepare("SELECT nom,prenom FROM utilisateur WHERE id_user = ?");
            $req->execute([$_SESSION['id_user']]);
            $donnees = $req->fetch();
            echo $donnees['nom'], " ", $donnees['prenom'];
            ?>
            <br/>
            <a href="deconnexion.php">Déconnexion</a>
            <br/>
            <a href="update_password.php">Modifier le mot de passe</a>
            <br/>
            <a href="ajout_reservation.php">Prendre une réservation</a><br/>

            <?php
    // Récupérez les courts du club de l'utilisateur pour donner la possibilité de manager les terrains et membres
    $req_courts = $conn->prepare("SELECT c.id_court, c.emplacement, cl.nom_club AS nom_club, cl.ville AS ville, c.type_surface 
    FROM courts c 
    INNER JOIN club cl ON c.id_club = cl.id_club 
    WHERE c.id_club = ?");
    $req_courts->execute([$_SESSION['id_user']]);
    $courts = $req_courts->fetchAll(PDO::FETCH_ASSOC); 

    if ($courts) { ?>
        <a href="gestion_courts.php">Gestion des courts</a><br/>
        <a href="gestion_adherents.php">Gestion des adhérents</a><br/>
        <a href="statistique.php">Statistiques du club</a><br/>

    <?php } ?>
        <!-- si non-connecté on affiche la possibilité de se connecter -->
        <?php else : ?>
            <a href="connexion.php" >Connexion</a>
        <?php endif; ?>
    </h3>
</div>


<!-- liste récupérant tous les noms de club, en faire une liste et récupérer quel agenda de club afficher -->
<form id="clubForm" action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
        <?php
        require 'db_connect.php';
        $sqlUpdateEvent = "SELECT * FROM club";
        $stmt = $conn->prepare($sqlUpdateEvent);
        $stmt->execute();
        $clubs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo '<select id="liste_club" name="liste_club">';
        foreach ($clubs as $club) {
            echo '<option value="' . htmlspecialchars($club["nom_club"], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($club["nom_club"], ENT_QUOTES, 'UTF-8') . '</option>';
        }
        echo '</select>';
        
        echo '<button type="submit">Envoyer</button>';

        
        ?>
        </form></body><br>
        <!-- affichage des bouttons pour les différentes vues -->
<h3>Calendrier de la journée</h3>
<div class="menu_calendar">
    <button class="button" id="daily-btn">Vue quotidienne</button>
    <button class="button" id="weekly-btn">Vue hebdomadaire</button>
    <button class="button" id="monthly-btn">Vue mensuelle</button>
</div>


<div id="calendar"></div>
</body>
</html>