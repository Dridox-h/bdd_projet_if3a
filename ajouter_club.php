<?php
// Inclure le fichier de connexion à la base de données
include 'db_connect.php';

session_start(); 

$id_user = $_SESSION['id_user'];

// Vérifier si la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données soumises
    $nom_club = htmlspecialchars($_POST['nom_club'], ENT_QUOTES, 'UTF-8');
    $ville_club = htmlspecialchars($_POST['ville_club'], ENT_QUOTES, 'UTF-8');

    // Vérifier que les champs ne sont pas vides
    if (empty($nom_club) || empty($ville_club)) {
        echo "Tous les champs sont requis.";
        exit();
    }

    try {
        // Commencer une transaction
        $conn->beginTransaction();

        // Insérer le club dans la table des clubs
        $sql_insert_club = "INSERT INTO club (nom_club, ville) VALUES (?, ?)";
        $stmt_insert_club = $conn->prepare($sql_insert_club);
        $stmt_insert_club->execute([$nom_club, $ville_club]);

        // Récupérer l'ID du club nouvellement inséré
        $club_id = $conn->lastInsertId();

        // Insérer l'appartenance du club pour l'utilisateur avec le rôle admin
        $sql_insert_appartenance = "INSERT INTO appartenance_club (id_user, id_club, role_adherent) VALUES (?, ?, 'admin')";
        $stmt_insert_appartenance = $conn->prepare($sql_insert_appartenance);
        $stmt_insert_appartenance->execute([$id_user, $club_id]);

        // Valider la transaction
        $conn->commit();
        header("Location: index.php");

    } catch (PDOException $e) {
        // En cas d'erreur, annuler la transaction
        $conn->rollBack();
        echo "Erreur lors de l'ajout du club : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un club</title>
</head>
<body>
    <div id ="MenuBarre">
        <a href="index.php">Page d'accueil</a>
    </div>
    <h1>Ajouter un club</h1>
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="nom_club">Nom du club :</label><br>
        <input type="text" id="nom_club" name="nom_club" required><br><br>

        <label for="ville_club">Ville :</label><br>
        <input type="text" id="ville_club" name="ville_club" required><br><br>

        <input type="submit" value="Ajouter le club">
    </form>
</body>
</html>
