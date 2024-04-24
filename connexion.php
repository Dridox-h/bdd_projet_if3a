<?php
session_start(); 

$message = '';

// Connexion à la base de données
try {
    $bdd = new PDO("mysql:host=localhost;dbname=tennis;charset=utf8", "root", "");
} catch (PDOException $e) {
    die('Erreur de connexion : ' . $e->getMessage());
}

if (isset($_POST['submit'])) {
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $req = $bdd->prepare("SELECT * FROM utilisateur WHERE email = ?");
        $req->execute([$email]);
        $user = $req->fetch(); 

        // Check if user exists and if password matches
        if ($user) {
            // Verify password
            if ($password == $user['password']) {
                // Password is correct, set session variable and redirect to index.php
                session_start();
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
<body>
    <h1>Connexion</h1>
    <?php if (!empty($message)): ?>
        <p style="color:red"><?= $message ?></p>
    <?php endif; ?>
    <form class="form-container" action="connexion.php" method="post">
        <label for="email">Email : </label>
        <input type="email" name="email" id="email" required>
        <label for="password">Mot de passe</label>
        <input type="password" name="password" id="password" required>
        <input type="submit" name="submit" value="Connexion">
        Vous n'êtes pas encore inscrit ? <a href="inscription.php">S'inscrire </a>
    </form>
</body>
</html>