<?php
// Inclure le fichier de connexion à la base de données
include 'db_connect.php';

session_start(); 

$id_user = $_SESSION['id_user'];

// Vérifier si la requête est de type POST pour l'ajout ou la suppression de club
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Vérifier si l'utilisateur souhaite ajouter un club
    if (isset($_POST['add_club'])) {
        // Ajout du club
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

    } elseif (isset($_POST['delete_club'])) { // Vérifier si l'utilisateur souhaite supprimer un club
        $club_id_to_delete = $_POST['delete_club'];

        try {
            // Commencer une transaction
            $conn->beginTransaction();

            // Supprimer les associations de club dans la table des appartenances
            $sql_delete_appartenance = "DELETE FROM appartenance_club WHERE id_club = ?";
            $stmt_delete_appartenance = $conn->prepare($sql_delete_appartenance);
            $stmt_delete_appartenance->execute([$club_id_to_delete]);

            // Supprimer le club de la table des clubs
            $sql_delete_club = "DELETE FROM club WHERE id_club = ?";
            $stmt_delete_club = $conn->prepare($sql_delete_club);
            $stmt_delete_club->execute([$club_id_to_delete]);

            // Valider la transaction
            $conn->commit();
            header("Location: index.php");

        } catch (PDOException $e) {
            // En cas d'erreur, annuler la transaction
            $conn->rollBack();
            echo "Erreur lors de la suppression du club : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter ou supprimer un club</title>
    <link href="stylesheet/styles.css" rel="stylesheet">
</head>
<body>
    <div id="MenuBarre">
        <a href="index.php">Page d'accueil</a>
    </div>
    <h1>Ajouter un club</h1><!-- form qui va recuillir les données à ajouter pour la création d'un club -->
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="nom_club">Nom du club :</label><br>
        <input type="text" id="nom_club" name="nom_club" required><br><br>

        <label for="ville_club">Ville :</label><br>
        <input type="text" id="ville_club" name="ville_club" required><br><br>

        <input type="submit" name="add_club" value="Ajouter le club">
    </form>

    <h1>Supprimer un club</h1>
    <!-- form pour supprimer un club -->
    <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="delete_club">Sélectionnez le club à supprimer :</label><br>
        <select id="delete_club" name="delete_club">
            <?php
            // on récupère les clubs de l'utilisateur où l'utilisateur est admin
                $sql_clubs_utilisateur = "SELECT club.id_club, club.nom_club FROM club
                INNER JOIN appartenance_club ON club.id_club = appartenance_club.id_club
                WHERE appartenance_club.id_user = ? AND appartenance_club.role_adherent = 'admin'";
                $stmt_clubs_utilisateur = $conn->prepare($sql_clubs_utilisateur);
                $stmt_clubs_utilisateur->execute([$id_user]);
                $clubs_utilisateur = $stmt_clubs_utilisateur->fetchAll(PDO::FETCH_ASSOC);

                // Afficher les options du menu déroulant pour la sélection du club à supprimer
                foreach ($clubs_utilisateur as $club) {
                echo '<option value="' . $club['id_club'] . '">' . $club['nom_club'] . '</option>';
                }
                    ?>
        </select><br><br>
        <!--On demande la revérification par l'utilisateur de vouloir supprimer-->
        <input type="hidden" name="id_adherent" value="Supprimer le club">
        <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce club ?')">Supprimer le club</button>
    </form>
</body>
</html>
