<?php
session_start(); 
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Réservation de court de Tennis</title>
    <link rel="stylesheet" href="./stylesheet/styles.css">

</head>
<body>
    <?php if (isset($_SESSION['id_user'])) : ?>
        <h1>Bienvenue sur le site de réservation de courts de tennis !</h1>
    <?php else : ?>
        <h1>Connexion</h1>
        <form class="form-container" action="connexion.php" method="post">
            <label for="email">Email : </label>
            <input type="email" name="email" id="email" required>
            <label for="password">Mot de passe</label>
            <input type="password" name="password" id="password" required>
            <input type="submit" name="submit" value="Connexion">
            Vous n'êtes pas encore inscrit ? <a href="inscription.php">S'inscrire </a>
        </form>
    <?php endif; ?>
</body>
</html>
