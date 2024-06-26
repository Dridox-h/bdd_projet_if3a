<?php
include 'db_connect.php';
session_start(); 
$id_user = $_SESSION['id_user'];
$req_club = $conn->prepare("SELECT id_club FROM appartenance_club WHERE id_user = ? AND role_adherent = 'admin'");
$req_club->execute([$id_user]);
$club_utilisateur = $req_club->fetch(PDO::FETCH_ASSOC);

// Vérifiez si l'utilisateur est administrateur d'un club
if ($club_utilisateur) {

    // Récupérez les adherents du club de l'utilisateur
    $req_adherent = $conn->prepare("SELECT ac.id_user AS id_user, u.nom AS nom, u.prenom AS prenom, c.id_club AS id_club, c.nom_club AS nom_club, ac.role_adherent AS role 
                          FROM appartenance_club ac 
                          INNER JOIN utilisateur u ON ac.id_user = u.id_user 
                          INNER JOIN club c ON c.id_club = ac.id_club
                          WHERE c.id_club = ? ");
    $req_adherent->execute([$club_utilisateur['id_club']]);
    $adherents = $req_adherent->fetchAll(PDO::FETCH_ASSOC); 
} 
if (isset($_POST['id_adherent'])) {
    $id_adherent = $_POST['id_adherent'];
    $req = $conn->prepare("DELETE FROM appartenance_club WHERE id_user = ?");
    $req->execute([$id_adherent]);
}
$req = $conn->prepare("SELECT ac.id_user AS id_user, u.nom AS nom, u.prenom AS prenom, c.id_club AS id_club, c.nom_club AS nom_club, ac.role_adherent AS role 
    FROM appartenance_club ac 
    INNER JOIN utilisateur u ON ac.id_user = u.id_user 
    INNER JOIN club c ON c.id_club = ac.id_club
    WHERE c.id_club = ? ");
$req->execute([$club_utilisateur['id_club']]);
$adherents = $req->fetchAll(PDO::FETCH_ASSOC); 

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des adherents de votre club</title>
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


    <?php if ($club_utilisateur) : ?>
        <h1>Tableau des adherents de votre club</h1>
        <table>
        <thead>
            <tr>
                <th>Nom</th>
                <th>Prénom</th>
                <th>Role</th>
                <th>Expulsion</th>


            </tr>
        </thead>
        <tbody> <!-- on affiche tous les adhérents du club-->
        <?php foreach ($adherents as $adherent) { ?>
                <tr>
                    <td><?php echo $adherent['nom']; ?></td>
                    <td><?php echo $adherent['prenom']; ?></td>
                    <td><?php echo $adherent['role']; ?></td>
                    <td>
                        <?php if ($id_user != $adherent["id_user"]){?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="id_adherent" value="<?php echo $adherent['id_user']; ?>">
                                <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir expulser cette adherent ?')">Expulser</button>
                            </form>
                        <?php } else { ?>
                               Vous ne pouvez pas vous expulser vous-même.
                        <?php } ?>                    
                    </td>
                </tr>
        <?php } ?>
        </tbody>
        </table>
        <a href="ajout_adherent.php">Ajouter des adhérents</a>
    <?php else: ?><!-- si non-administrateur on propose la connexion -->
    <p>Vous n'êtes pas administrateur d'un club. <a href='connexion.php'>Connectez-vous</a> pour accéder à cette page.</p>
    <?php endif; ?>
</body>
</html>
