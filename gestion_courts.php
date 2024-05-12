<?php
include 'db_connect.php';
session_start(); 

// récupère les id des clubs où l'utilisateur est admin 
$id_user = $_SESSION['id_user'];
$req_club = $conn->prepare("SELECT id_club FROM appartenance_club WHERE id_user = ? AND role_adherent = 'admin'");
$req_club->execute([$id_user]);
$club_utilisateur = $req_club->fetch(PDO::FETCH_ASSOC);

// Vérifiez si l'utilisateur est administrateur d'un club
if ($club_utilisateur) {

    // Récupérez les courts du club de l'utilisateur
    $req_courts = $conn->prepare("SELECT c.id_court, c.emplacement, cl.nom_club AS nom_club, cl.ville AS ville, c.type_surface 
                          FROM courts c 
                          INNER JOIN club cl ON c.id_club = cl.id_club 
                          WHERE c.id_club = ?");
    $req_courts->execute([$club_utilisateur['id_club']]);
    $courts = $req_courts->fetchAll(PDO::FETCH_ASSOC); 
} else {
    echo "<p>Vous n'êtes pas administrateur d'un club. <a href='connexion.php'>Connectez-vous</a> pour accéder à cette page.</p>";
}

// on supprime le courts si le bouton est pressé 
if (isset($_POST['id_court'])) {
    $id_court = $_POST['id_court'];
    $req = $conn->prepare("DELETE FROM courts WHERE id_court = ?");
    $req->execute([$id_court]);
}

$req = $conn->prepare("SELECT c.id_court, c.emplacement, cl.nom_club AS nom_club, cl.ville AS ville, c.type_surface, c.etat AS etat
                      FROM courts c 
                      INNER JOIN club cl ON c.id_club = cl.id_club
                      WHERE c.id_club = ?"); 
$req->execute([$club_utilisateur['id_club']]);
$courts = $req->fetchAll(PDO::FETCH_ASSOC); 
?>

<!DOCTYPE html>
<html lang="fr">
<head>

    <meta charset="UTF-8">
    <title>Tableau des courts</title>
    <!-- ajout du style ponctuel -->
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
    <div id ="MenuBarre"><!-- barre de navigation-->
        <a href="index.php">Page d'accueil</a>
    </div>

    <h1>Tableau des courts</h1>

    <?php if ($club_utilisateur) { ?>
        <table>
        <thead>
            <tr>
                <th>Emplacement</th>
                <th>Nom du club</th>
                <th>Type de surface</th>
                <th>Ville</th>
                <th>Etat</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- on affiche tous les courts disponibles-->
        <?php foreach ($courts as $court) { ?>
                <tr>
                    <td><?php echo $court['emplacement']; ?></td>
                    <td><?php echo $court['nom_club']; ?></td>
                    <td><?php echo $court['type_surface']; ?></td>
                    <td><?php echo $court['ville']; ?></td>
                    <td><?php echo $court['etat'] == 0 ? "Bloqué" : "Disponible"; ?></td>
                    <td>
                        <a href="modifier_court.php?id=<?php echo $court['id_court']; ?>">Modifier</a>
                        <form method="POST" style="display:inline;">
                        <!-- confirmation de la suppression et envoie des données sur la même page-->
                            <input type="hidden" name="id_court" value="<?php echo $court['id_court']; ?>">
                            <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce court ?')">Supprimer</button>
                        </form>
                    </td>
                </tr>
        <?php } ?>
        </tbody>
        </table>
    <?php } ?>
    <a href="ajouter_court.php">Ajouter une court</a>

    </body>
</html>
