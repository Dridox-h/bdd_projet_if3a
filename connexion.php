<?php
session_start(); 

$message = '';

// Connexion à la base de données
try {
    $bdd = new PDO("mysql:host=localhost;dbname=tennis;charset=utf8", "root", "");
} catch (PDOException $e) {
    die('Erreur de connexion : ' . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['email']) && isset($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        $req = $pdo->prepare("SELECT * FROM utilisateur WHERE email = :email");
        $req->execute(['email' => $email]);
        $user = $req->fetch();

        // Vérifie si l'utilisateur existe et si le mot de passe correspond
        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['id_user'] = $user['id_user'];
            header("Location: accueil.php"); 
            exit();
        } else {
            $message = 'Mauvais identifiants';
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