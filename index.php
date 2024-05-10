<?php
$pdo = new PDO("mysql:host=localhost;dbname=tennis;charset=utf8", "root", "");
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 

$today = date('Y-m-d');

$query = "SELECT * FROM reservation INNER JOIN courts c ON c.id_court=reservation.id_court 
INNER JOIN club cl ON cl.id_club=c.id_club WHERE cl. nom_club = ? ";

$stmt = $pdo->prepare($query);
$stmt->execute([$_POST['liste_club']]);
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

$events_json = json_encode($events);



?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendrier de la journée</title>
    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css' rel='stylesheet' />
    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.print.min.css' rel='stylesheet' media='print' />
    <script src='https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment-with-locales.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/locale/fr.js'></script>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.min.css' rel='stylesheet' />
    <link href='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.10.2/fullcalendar.print.min.css' rel='stylesheet' media='print' />
    <script>
        $(document).ready(function() {
            var events = <?php echo $events_json; ?>;
            events.forEach(function(event) {
                var start = moment(event.start);
                var end = moment(event.start).add(event.duree, 'minutes');
                event.start = start.format();
                event.end = end.format();
                event.title = event.club;
                event.description = event.club;
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
<div class="header-menu">
    <div>
        <a href="connexion.php">se connecter</a>
        <a href="inscription.php">s'inscrire</a>
        <a href="update_password.php">modifier mdp</a>
    </div>
</div>

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
<h3>Calendrier de la journée</h3>
<div class="menu_calendar">
    <button class="button" id="daily-btn">Vue quotidienne</button>
    <button class="button" id="weekly-btn">Vue hebdomadaire</button>
    <button class="button" id="monthly-btn">Vue mensuelle</button>
</div>
<div id="calendar"></div>
</body>
</html>