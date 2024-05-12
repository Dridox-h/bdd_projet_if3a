<?php

if (isset($_GET['id'])) {
    $court_id = $_GET['id'];

    try { // connexion à la bdd
        $bdd = new PDO("mysql:host=localhost;dbname=tennis;charset=utf8", "root", "");
        $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
    } catch (PDOException $e) {
        die('Erreur de connexion : ' . $e->getMessage());
    }

    $req = $bdd->prepare("SELECT c.emplacement, cl.nom_club AS nom_club, cl.ville AS ville, c.type_surface 
                          FROM courts c 
                          INNER JOIN club cl ON c.id_club = cl.id_club
                          WHERE c.id_court = :court_id");
    $req->bindParam(':court_id', $court_id);
    $req->execute();
    $court = $req->fetch(PDO::FETCH_ASSOC); 

    if (!$court) {
        die('Court non trouvé.');
    }
} else {
    header('Location: gestion_courts.php');
    exit;
}
// on récupère les données envoyées
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $emplacement = $_POST['emplacement'];
    $type_surface = $_POST['type_surface'];

    try { // on update les courts en fonction des données
        $update_req = $bdd->prepare("UPDATE courts 
                                    SET emplacement = :emplacement, type_surface = :type_surface 
                                    WHERE id_court = :court_id");
        $update_req->bindParam(':emplacement', $emplacement);
        $update_req->bindParam(':type_surface', $type_surface);
        $update_req->bindParam(':court_id', $court_id);
        $update_req->execute();

        header('Location: gestion_courts.php');
        exit;
    } catch (PDOException $e) {
        echo "Erreur lors de la mise à jour du court : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier le court</title>
</head>
<body>
    <div id ="MenuBarre">
        <a href="index.php">Page d'accueil</a>
    </div>
<h2>Modifier le court</h2>

<form method="post"> <!--on fait un formulaire pour récupèrer les données -->
    <label for="emplacement">Emplacement:</label><br>
    <input type="text" id="emplacement" name="emplacement" value="<?php echo $court['emplacement']; ?>"><br>
    <label for="type_surface">Type de surface:</label><br>
    <input type="text" id="type_surface" name="type_surface" value="<?php echo $court['type_surface']; ?>"><br><br>
    <input type="submit" value="Modifier">
</form>

</body>
</html>
