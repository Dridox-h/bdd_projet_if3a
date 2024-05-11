<?php
session_start();
include 'db_connect.php';
function email_present($email, $bdd) {
    $query = $bdd->prepare("SELECT email FROM utilisateur WHERE email = :email");

    $query->execute(['email' => $email]);

    $result = $query->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        return 0; // L'e-mail est déjà présent
    } else {
        return 1; // L'e-mail n'est pas présent
    }
}


try {
    $bdd = new PDO("mysql:host=localhost;dbname=tennis;charset=utf8", "root", "");


} catch (PDOException $e) {
    die('Erreur de connexion : ' . $e->getMessage());
}


if (isset($_POST['submit'])) {
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    if(email_present($email,$bdd)==0){
        echo "email déjà présent";
    }else {
        $req = $bdd->prepare("INSERT INTO utilisateur(nom, prenom, email, password) VALUES (?,?,?,?);");
        $req->execute([$nom, $prenom,$email,$password]);
        $req=$bdd->prepare("SELECT id_user FROM utilisateur WHERE email = ?");
        $req->execute([$email]);
        $id_user = $req->fetch()["id_user"];
        if (isset($_POST['liste_club'])) {
            $clubs = $_POST['liste_club'];
            if (is_array($clubs)){
                foreach ($clubs as $club) {
                    $req = $bdd->prepare("SELECT id_club FROM club WHERE nom_club = ?");
                    $req->execute([$club]);
                    $id_club = $req->fetch(PDO::FETCH_ASSOC)['id_club'];
                    $req = $bdd->prepare("INSERT INTO appartenance_club(id_user, id_club) VALUES (?,?)");
                    $req->execute([$id_user, $id_club]);
                }
            }else{
                $req = $bdd->prepare("SELECT id_club FROM club WHERE nom_club = ?");
                $req->execute([$clubs]);
                $id_club = $req->fetch(PDO::FETCH_ASSOC)['id_club'];
                $req = $bdd->prepare("INSERT INTO appartenance_club(id_user, id_club) VALUES (?,?)");
                $req->execute([$id_user, $id_club]);
            }
        }
        $_SESSION['id_user'] = $id_user;
        header("Location: index.php");
        exit();
    }

}



//var_dump($_POST);


?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="./stylesheet/styles.css">
</head>

<body>
    <div id ="MenuBarre">
        <a href="index.php">Page d'accueil</a>
    </div>
    <section class="section-container">
        <h1>Inscription</h1>
        <form class="form-container" action="inscription.php" method="post">
            <label for="nom">Nom</label>
            <input type="text" name="nom" id="nom" required>
            <label for="prenom">Prénom</label>
            <input type="text" name="prenom" id="prenom" required>
            <label for="email">E-mail</label>
            <input type="email" name="email" id="email" required>
            <label for="password">Mot de passe</label>
            <input type="password" name="password" id="password" required>
            <br/>
            <label for="liste_club">Clubs</label>
            <?php
            // Récupérez la liste des clubs pour créer le menu déroulant
            $stmt = $conn->prepare("SELECT * FROM club");
            $stmt->execute();
            $clubs = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo '<select multiple id="liste_club" name="liste_club">';
            foreach ($clubs as $club) {
                echo '<option value="' . htmlspecialchars($club["nom_club"], ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($club["nom_club"], ENT_QUOTES, 'UTF-8') . '</option>';
            }
            echo '</select>';?>
            <br/>
            <input type="submit" name="submit" value="S'inscrire">
            Vous avez déjà un compte ? <a href="connexion.php">Se connecter </a>
        </form>
    </section>

</body>

</html>