<?php
try {
    $bdd = new PDO("mysql:host=localhost;dbname=tennis;charset=utf8", "root", "");
} catch (PDOException $e) {
    die('Erreur de connexion : ' . $e->getMessage());
}
session_start(); 
$id_user = $_SESSION['id_user'];
$req_club = $bdd->prepare("SELECT id_club FROM appartenance_club WHERE id_user = ? AND role_adherent = 'admin'");
$req_club->execute([$id_user]);
$club_utilisateur = $req_club->fetch(PDO::FETCH_ASSOC);

// Vérifiez si l'utilisateur est administrateur d'un club
if ($club_utilisateur) {
    $req_courts = $bdd->prepare("SELECT c.id_court, c.emplacement, cl.nom_club AS nom_club, cl.ville AS ville, c.type_surface 
                          FROM courts c 
                          INNER JOIN club cl ON c.id_club = cl.id_club 
                          WHERE c.id_club = ?");
    $req_courts->execute([$club_utilisateur['id_club']]);
    $courts = $req_courts->fetchAll(PDO::FETCH_ASSOC);

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $emplacement = $_POST['emplacement'];
        $type_surface = $_POST['type_surface'];
        $id_club = $club_utilisateur['id_club'];

        $req = $bdd->prepare("INSERT INTO courts (emplacement, id_club, type_surface) VALUES (?, ?, ?)");
        $req->execute([$emplacement, $id_club, $type_surface]);

        header("Location: gestion_courts.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un court</title>
    <link href="stylesheet/styles.css" rel="stylesheet">
</head>
<body>
    <div id ="MenuBarre">
        <a href="index.php">Page d'accueil</a>
    </div>
    <?if ($club_utilisateur) {?>
    <h2>Ajouter un court à votre club </h2>

    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="emplacement">Emplacement:</label><br>
        <input type="text" id="emplacement" name="emplacement" required><br><br>

        <label for="type_surface">Type de surface:</label><br>
        <input type="text" id="type_surface" name="type_surface" required><br><br>

        <input type="submit" value="Ajouter">
    </form>
    <?php
    } else {
        echo "<p>Vous n'êtes pas administrateur d'un club. <a href='connexion.php'>Connectez-vous</a> pour accéder à cette page.</p>";
    }
    ?>
</body>
</html>