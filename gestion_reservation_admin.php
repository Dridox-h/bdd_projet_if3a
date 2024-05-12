<?php
include 'db_connect';
session_start(); 

// préparation de la requête pour savoir si un user est un admin
$id_user = $_SESSION['id_user'];
$req_club = $conn->prepare("SELECT id_club FROM appartenance_club WHERE id_user = ? AND role_adherent = 'admin'");
$req_club->execute([$id_user]);
$club_utilisateur = $req_club->fetch(PDO::FETCH_ASSOC);

// Vérifiez si l'utilisateur est administrateur d'un club
if ($club_utilisateur) {

    // Récupérez les adherents du club de l'utilisateur
    $req_reservation = $conn->prepare("SELECT c.emplacement AS emplacement , r.date_reservation AS date , r.duree AS duree , r.heure_debut AS heure, u.nom AS nom, u.prenom AS prenom
                                    FROM reservation r 
                                    INNER JOIN inscrits i ON r.id_reservation = i.id_reservation
                                    INNER JOIN courts c ON c.id_court = r.id_court
                                    INNER JOIN utilisateur u ON u.id_user = i.id_user
                                    WHERE i.role = 'organisateur' AND c.id_club = ?");
    $req_reservation->execute([$club_utilisateur['id_club']]);
    $reservations = $req_reservation->fetchAll(PDO::FETCH_ASSOC); 
} else {
    echo "<p>Vous n'êtes pas administrateur d'un club. <a href='connexion.php'>Connectez-vous</a> pour accéder à cette page.</p>";
}


if (isset($_POST['id_reservation'])) {
    $id_reservation = $_POST['id_reservation'];
    $req = $conn->prepare("DELETE FROM reservation WHERE id_reservation = ?");
    $req->execute([$id_reservation]);
}

$req = $conn->prepare("SELECT c.emplacement AS emplacement , r.date_reservation AS date , r.duree AS duree , r.heure_debut AS heure, u.nom AS nom, u.prenom AS prenom
                      FROM reservation r 
                      INNER JOIN inscrits i ON r.id_reservation = i.id_reservation
                      INNER JOIN courts c ON c.id_court = r.id_court
                      INNER JOIN utilisateur u ON u.id_user = i.id_user
                      WHERE i.role = 'organisateur'"); 
$req->execute();
$courts = $req->fetchAll(PDO::FETCH_ASSOC); 
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau des réservation</title>
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
<h1>Tableau des réservation de votre club </h1>

<?php if ($club_utilisateur) { ?>
    <table>
    <thead>
        <tr>
            <th>Emplacement</th>
            <th>Date</th>
            <th>Heure de début</th>
            <th>Durée</th>
            <th>Nom et Prenom de l'organisateur</th>
            <th>Supprimer</th> 


        </tr>
    </thead>
    <tbody>
    <?php foreach ($reservations as $reservation) { ?>
            <tr>
                <td><?php echo $court['emplacement']; ?></td>
                <td><?php echo $court['date']; ?></td>
                <td><?php echo $court['heure']; ?></td>
                <td><?php echo $court['duree']; ?></td>
                <td><?php echo $court['nom'],$court['prenom']; ?></td>

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
