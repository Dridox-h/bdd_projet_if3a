<?php
session_start(); 

include 'db_connect.php';

// on vérifie que les mots de passes et les emails correspondent pour autoriser la connexion
if (isset($_POST['submit'])) {
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $req = $conn->prepare("SELECT * FROM utilisateur WHERE email = ?");
        $req->execute([$email]);
        $user = $req->fetch(); 

        if ($user) {
            // si le mot de passe est bon on envoie la personne sur index.php
            if ($password == $user['password']) {
                $_SESSION['id_user'] = $user['id_user'];
                header("Location: index.php");
                exit();
            } else {
                $message = 'Mauvais mot de passe';
            }
        } else {
            $message = 'Utilisateur non trouvé';
        }
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
    <link rel="stylesheet" href="./stylesheet/styles.css">

</head>
<body> <!-- barre d'accueil-->
    <div id ="MenuBarre">
        <a href="index.php">Page d'accueil</a>
    </div>
    <h1>Connexion</h1>
    <?php if (!empty($message)): ?>
        <p style="color:red"><?= $message ?></p>
    <?php endif; ?>
    <!-- form pour récupérer les données de connexions-->
    <form class="form-container" action="connexion.php" method="post">
        <label for="email">Email : </label>
        <input type="email" name="email" id="email" required>
        <label for="password">Mot de passe</label>
        <input type="password" name="password" id="password" required>
        <input type="submit" name="submit" value="Connexion">
        <!-- propositions d'inscription-->
        Vous n'êtes pas encore inscrit ? <a href="inscription.php">S'inscrire </a>
    </form>
</body>
</html>