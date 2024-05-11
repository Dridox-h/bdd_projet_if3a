<?php
session_start(); 

$message = '';

if (isset($_SESSION['id_user'])) {
    try {
        $bdd = new PDO("mysql:host=localhost;dbname=tennis;charset=utf8", "root", "");
    } catch (PDOException $e) {
        die('Erreur de connexion : ' . $e->getMessage());
    }

    $id_user = $_SESSION['id_user'];

    $stmt = $bdd->prepare("SELECT nom, prenom FROM utilisateur WHERE id_user = ?");
    $stmt->execute([$id_user]);
    $user_info = $stmt->fetch();
    $nom = $user_info['nom'];
    $prenom = $user_info['prenom'];

    if (isset($_POST['submit'])) {
        if (isset($_POST['ancient_password']) && isset($_POST['new_password'])) {
            $ancient_password = $_POST['ancient_password'];
            $new_password = $_POST['new_password'];

            $req = $bdd->prepare("SELECT * FROM utilisateur WHERE id_user = ?");
            $req->execute([$id_user]);
            $user = $req->fetch();

            if ($ancient_password == $user['password']) {
                if ($ancient_password !== $new_password) {
                    $req = $bdd->prepare("UPDATE utilisateur SET password = ? WHERE id_user = ? ");
                    $req->execute([$new_password, $id_user]);
                    $message = 'Mot de passe mis à jour avec succès';
                } else {
                    $message = 'Le nouveau mot de passe doit être différent de l\'ancien';
                }
            } else {
                $message = 'Mot de passe actuel incorrect';
            }
        }
    }
} else {
    header("Location: connexion.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Modification du mot de passe</title>
    <link rel="stylesheet" href="./stylesheet/styles.css">
</head>
<body>
    <div id ="MenuBarre">
        <a href="index.php">Page d'accueil</a>
    </div>
    <h1>Modifiez votre mot de passe, <?= isset($prenom) ? $prenom : '' ?> <?= isset($nom) ? $nom : '' ?></h1>
    <?php if (!empty($message)): ?>
        <p style="color:red"><?= $message ?></p>
    <?php endif; ?>
    <form class="form-container" action="update_password.php" method="post">
        <label for="ancient_password">Ancien mot de passe</label>
        <input type="password" name="ancient_password" id="ancient_password" required>
        <label for="new_password">Nouveau mot de passe</label>
        <input type="password" name="new_password" id="new_password" required>
        <input type="submit" name="submit" value="Modifier">
    </form>
</body>
</html>
