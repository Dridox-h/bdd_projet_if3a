<?php
include 'db_connect.php';
session_start(); 

// préparation de la requête pour savoir si un user est un admin
$id_user = $_SESSION['id_user'];
$req_club = $conn->prepare("SELECT id_club FROM appartenance_club WHERE id_user = ? AND role_adherent = 'admin'");
$req_club->execute([$id_user]);
$club_utilisateur = $req_club->fetch(PDO::FETCH_ASSOC);

// Vérifiez si l'utilisateur est administrateur d'un club
if ($club_utilisateur) {

    // Récupérez les adherents du club de l'utilisateur
    $req_reservation = $conn->prepare("SELECT r.id_reservation, c.emplacement AS emplacement , r.start_datetime AS debut , r.end_datetime AS fin, u.nom AS nom, u.prenom AS prenom
                                    FROM reservation r 
                                    INNER JOIN inscrits i ON r.id_reservation = i.id_reservation
                                    INNER JOIN courts c ON c.id_court = r.id_court
                                    INNER JOIN utilisateur u ON u.id_user = i.id_user
                                    WHERE i.role = 'leader' AND c.id_club = ?");
    $req_reservation->execute([$club_utilisateur['id_club']]);
    $reservations = $req_reservation->fetchAll(PDO::FETCH_ASSOC); 
} else {
    echo "<p>Vous n'êtes pas administrateur d'un club. <a href='connexion.php'>Connectez-vous</a> pour accéder à cette page.</p>";
}


if (isset($_POST['id_reservation'])) {
    $id_reservation = $_POST['id_reservation'];
    $req_inscrits = $conn->prepare("DELETE FROM inscrits WHERE id_reservation = ?"); //On supprime d'abords les inscrits 
    $req_inscrits->execute([$id_reservation]);
    $req = $conn->prepare("DELETE FROM reservation WHERE id_reservation = ?"); // puis la reservation
    $req->execute([$id_reservation]);
}

$req = $conn->prepare("SELECT r.id_reservation, c.emplacement AS emplacement , r.start_datetime AS debut , r.end_datetime AS fin, u.nom AS nom, u.prenom AS prenom
                      FROM reservation r 
                      INNER JOIN inscrits i ON r.id_reservation = i.id_reservation
                      INNER JOIN courts c ON c.id_court = r.id_court
                      INNER JOIN utilisateur u ON u.id_user = i.id_user
                      WHERE i.role = 'leader'AND c.id_club = ?"); 
$req->execute([$club_utilisateur['id_club']]);
$reservations = $req->fetchAll(PDO::FETCH_ASSOC); 
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau des réservation</title>
    <link href="stylesheet/styles.css" rel="stylesheet">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid black;
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
<h1>Tableau des réservation de votre club </h1>

<?php if ($club_utilisateur) { ?>
    <table>
    <thead>
        <tr>
            <th>Emplacement</th>
            <th>Début</th>
            <th>Fin</th>
            <th>Nom et Prenom de l'organisateur</th>
            <th>Supprimer</th> 


        </tr>
    </thead>
    <tbody>
    <?php foreach ($reservations as $reservation) { ?>
            <tr>
                <td><?php echo $reservation['emplacement']; ?></td>
                <td><?php echo $reservation['debut']; ?></td>
                <td><?php echo $reservation['fin']; ?></td>
                <td><?php echo $reservation['nom'],' ',$reservation['prenom']; ?></td>

                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="id_reservation" value="<?php echo $reservation['id_reservation']; ?>">
                        <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette réservation ?')">Supprimer</button>
                    </form>
                </td>
            </tr>
    <?php } ?>
    </tbody>
    </table>
<?php } ?>

</body>
</html>
