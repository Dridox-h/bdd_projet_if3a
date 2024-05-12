<?php
include 'db_connect.php'; // Inclure la connexion à la base de données

session_start(); // Démarrer la session


$userId = $_SESSION['id_user'];

if (!isset($_SESSION['id_user'])) {
    header('Location: login.php');
    exit();
}

// Vérifier que la requête est de type POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo "Requête invalide.";
    exit();

}
// on récupère les noms des différents utilisateurs préalablement envoyés 
$j_1 = htmlspecialchars($_POST['joueur1'], ENT_QUOTES, 'UTF-8');
$j_2 = htmlspecialchars($_POST['joueur2'], ENT_QUOTES, 'UTF-8');
$j_3 = htmlspecialchars($_POST['joueur3'], ENT_QUOTES, 'UTF-8');

// récupération de l'id de la réservation
$id_reservation = $_SESSION['id_reservation'];

// Requête pour récupérer l'ID de chaque joueur
$sql_j1 = "SELECT id_user FROM utilisateur WHERE nom = ?";
$stmt_j1 = $conn->prepare($sql_j1);
$stmt_j1->execute([$j_1]);
$id_user_j1_row = $stmt_j1->fetch(); 

// si l'id n'est pas vide (donc l'utilisateur n'a pas renseigné une case vide), on récupère les id des utilisateurs
if($id_user_j1_row){
    $id_user_j1 = $id_user_j1_row['id_user'];
}
$sql_j2 = "SELECT id_user FROM utilisateur WHERE nom = ?";
$stmt_j2 = $conn->prepare($sql_j2);
$stmt_j2->execute([$j_2]);
$id_user_j2_row = $stmt_j2->fetch();

if($id_user_j2_row){
    $id_user_j2 = $id_user_j2_row['id_user'];
}
// on répète cette opération 3 fois pour inscrire les 3 personnes
$sql_j3 = "SELECT id_user FROM utilisateur WHERE nom = ?";
$stmt_j3 = $conn->prepare($sql_j3);
$stmt_j3->execute([$j_3]);
$id_user_j3_row = $stmt_j3->fetch();
if($id_user_j3_row){
    $id_user_j3 = $id_user_j3_row['id_user'];
}


// si les id ne sont pas valides on insère les personnes dans la table inscrits
if (!empty($j_1)) {

    $sql = "INSERT INTO inscrits (id_user,id_reservation,role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    $result = $stmt->execute([$id_user_j1,$id_reservation,'joueur']);

}

if (!empty($j_2)) {

    $sql = "INSERT INTO inscrits (id_user,id_reservation,role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    $result = $stmt->execute([$id_user_j2,$id_reservation,'joueur']);

}


if (!empty($j_3)) {

    $sql = "INSERT INTO inscrits (id_user,id_reservation,role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    $result = $stmt->execute([$id_user_j3,$id_reservation,'joueur']);

}

// enfin, on ajoute la personne qui a fait la réservation comme 'leader' de la reservation dans la table inscrit

$sql = "INSERT INTO inscrits (id_user,id_reservation,role) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);

$result = $stmt->execute([$userId,$id_reservation,'leader']);

// retour possible au menu
echo "réservation éffectué <br> vous pouvez revenir sur la page d'acceuil : ";
echo "<a href='index.php'> retour au menu </a>";

?>