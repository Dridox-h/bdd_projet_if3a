<?php
include "db_connect.php";
session_start(); 
$id_user = $_SESSION['id_user'];
$req_club = $conn->prepare("SELECT id_club FROM appartenance_club WHERE id_user = ? AND role_adherent = 'admin'");
$req_club->execute([$id_user]);
$club_utilisateur = $req_club->fetch(PDO::FETCH_ASSOC);

// Vérifiez si l'utilisateur est administrateur d'un club
if ($club_utilisateur) {

    // Récupérer la liste des utilisateurs disponibles pour l'ajout
    $req_utilisateurs = $conn->query("SELECT id_user, nom, prenom FROM utilisateur");
    $utilisateurs = $req_utilisateurs->fetchAll(PDO::FETCH_ASSOC);

    // Traitement du formulaire d'ajout d'adhérents
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Vérification si des utilisateurs ont été sélectionnés
        if (isset($_POST['adherents']) && is_array($_POST['adherents']) && count($_POST['adherents']) > 0) {
            $id_club = $club_utilisateur['id_club'];
            $adherents = $_POST['adherents'];
            $role = $_POST['role'];

            
            // Ajout des adhérents sélectionnés au club
            $stmt = $conn->prepare("INSERT INTO appartenance_club (id_user, id_club, role_adherent) VALUES (?, ?, ?)");
            foreach ($adherents as $id_user) {
                $stmt->execute([$id_user, $club_utilisateur['id_club'],$role]);
            }
            
            echo "<p>Les adhérents ont été ajoutés avec succès.</p>";
        } else {
            echo "<p>Veuillez sélectionner au moins un adhérent.</p>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajout d'adhérents</title>
    <link href="stylesheet/styles.css" rel="stylesheet">
</head>
<body>
    <!-- Barre d'affichage permettant le retour à l'accueil -->
<div id ="MenuBarre">
    <a href="index.php">Page d'accueil</a>
</div>
<!-- vérification de si l'utilisateur est un admin -->
<?php if ($club_utilisateur): ?>
    <h1>Ajouter des adhérents</h1>
<!-- form permettant de recuillir les données pour ajouter un utilisateurs à un club -->
    <form method="post">
        <!-- checkbox permettant de choisir le type d'utilisateur qu'on va ajouter-->
        <label for="role">Sélectionnez le rôle :</label><br>
        <input type="radio" id="administrateur" name="role" value="administrateur" required>
        <label for="administrateur">Administrateur</label><br>
        <input type="radio" id="adherent" name="role" value="adherent" required>
        <label for="adherent">Adhérent</label><br><br>

    <!-- liste permettant de choisir les utilisateurs -->
        <label for="adherents">Sélectionnez les adhérents à ajouter :</label><br>
        <select name="adherents[]" id="adherents" multiple>
            <?php foreach ($utilisateurs as $utilisateur): ?>
                <option value="<?php echo $utilisateur['id_user']; ?>"><?php echo $utilisateur['nom'] . ' ' . $utilisateur['prenom']; ?></option>
            <?php endforeach; ?>
        </select><br><br>
        <input type="submit" value="Confirmer l'ajout">
    </form>
    <a href="gestion_adherents.php">Retour à la liste des adhérents</a>
<?php else: ?><!-- si non-administrateur on propose la connexion -->
    <p>Vous n'êtes pas administrateur d'un club. <a href='connexion.php'>Connectez-vous</a> pour accéder à cette page.</p>
<?php endif; ?>
</body>
</html>
