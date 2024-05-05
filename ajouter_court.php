<?php
try {
    $bdd = new PDO("mysql:host=localhost;dbname=tennis;charset=utf8", "root", "");
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
} catch (PDOException $e) {
    die('Erreur de connexion : ' . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emplacement = $_POST['emplacement'];
    $type_surface = $_POST['type_surface'];
    $id_club = $_POST['id_club'];

    $req = $bdd->prepare("INSERT INTO courts (emplacement, id_club, type_surface) VALUES (?, ?, ?)");
    $req->execute([$emplacement, $id_club, $type_surface]);

    header("Location: gestion_courts.php");
    exit();
}

$req_clubs = $bdd->query("SELECT id_club, nom_club,ville FROM club");
$clubs = $req_clubs->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un court</title>
</head>
<body>

<h2>Ajouter un court</h2>

<form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <label for="emplacement">Emplacement:</label><br>
    <input type="text" id="emplacement" name="emplacement" required><br><br>

    <label for="type_surface">Type de surface:</label><br>
    <input type="text" id="type_surface" name="type_surface" required><br><br>

    <label for="id_club">Club:</label><br>
    <select id="id_club" name="id_club" required>
        <option value="">Sélectionnez un club</option>
        <?php foreach ($clubs as $club) { ?>
            <option value="<?php echo $club['id_club']; ?>">
                <?php echo $club['nom_club'] . ' (' . $club['ville'] . ')'; ?>
            </option>
        <?php } ?>
    </select><br><br>

    <input type="submit" value="Ajouter">
</form>

</body>
</html>



