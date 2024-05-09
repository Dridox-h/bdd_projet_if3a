<?php
try {
    $bdd = new PDO("mysql:host=localhost;dbname=tennis;charset=utf8", "root", "");
    $bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
} catch (PDOException $e) {
    die('Erreur de connexion : ' . $e->getMessage());
}

if (isset($_POST['id_court'])) {
    $id_court = $_POST['id_court'];
    $req = $bdd->prepare("DELETE FROM courts WHERE id_court = ?");
    $req->execute([$id_court]);
}

$req = $bdd->prepare("SELECT c.id_court, c.emplacement, cl.nom_club AS nom_club, cl.ville AS ville, c.type_surface 
                      FROM courts c 
                      INNER JOIN club cl ON c.id_club = cl.id_club"); 
$req->execute();
$courts = $req->fetchAll(PDO::FETCH_ASSOC); 
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Tableau des courts</title>
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

<h2>Tableau des courts</h2>
<table>
    <thead>
        <tr>
            <th>Emplacement</th>
            <th>Nom du club</th>
            <th>Type de surface</th>
            <th>Ville</th>
            <th>Actions</th> 
        </tr>
    </thead>
    <tbody>
        <?php foreach ($courts as $court) { ?>
            <tr>
                <td><?php echo $court['emplacement']; ?></td>
                <td><?php echo $court['nom_club']; ?></td>
                <td><?php echo $court['type_surface']; ?></td>
                <td><?php echo $court['ville']; ?></td>
                <td>
                    <a href="modifier_court.php?id=<?php echo $court['id_court']; ?>">Modifier</a>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="id_court" value="<?php echo $court['id_court']; ?>">
                        <button type="submit" onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce court ?')">Supprimer</button>
                    </form>
                </td>
            </tr>
        <?php } ?>

        
    </tbody>
</table>
<a href="ajouter_court.php">Ajouter une court</a>

</body>
</html>
