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

$j_1 = htmlspecialchars($_POST['joueur1'], ENT_QUOTES, 'UTF-8');
$j_2 = htmlspecialchars($_POST['joueur2'], ENT_QUOTES, 'UTF-8');
$j_3 = htmlspecialchars($_POST['joueur3'], ENT_QUOTES, 'UTF-8');

$id_reservation = $_SESSION['id_reservation'];

// Requête pour récupérer l'ID de chaque joueur
$sql_j1 = "SELECT id_user FROM utilisateur WHERE nom = ?";
$stmt_j1 = $conn->prepare($sql_j1);
$stmt_j1->execute([$j_1]);
$id_user_j1_row = $stmt_j1->fetch(); // Utilisez fetch() pour obtenir une seule ligne
$id_user_j1 = $id_user_j1_row['id_user']; // Récupérez l'ID de joueur 1

$sql_j2 = "SELECT id_user FROM utilisateur WHERE nom = ?";
$stmt_j2 = $conn->prepare($sql_j2);
$stmt_j2->execute([$j_2]);
$id_user_j2_row = $stmt_j2->fetch();
$id_user_j2 = $id_user_j2_row['id_user']; // Récupérez l'ID de joueur 2

$sql_j3 = "SELECT id_user FROM utilisateur WHERE nom = ?";
$stmt_j3 = $conn->prepare($sql_j3);
$stmt_j3->execute([$j_3]);
$id_user_j3_row = $stmt_j3->fetch();
$id_user_j3 = $id_user_j3_row['id_user']; // Récupérez l'ID de joueur 3

echo $j_1,$j_2,$j_3;

if (empty($j_3) &&(!empty($j_2) || !empty($j_1))) {
    echo "j-3 est vide";

    $sql = "INSERT INTO inscrits (id_user,id_reservation,role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    $result = $stmt->execute([$id_user_j1,$id_reservation,'joueur']);

    $sql = "INSERT INTO inscrits (id_user,id_reservation,role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);

    $result = $stmt->execute([$id_user_j2,$id_reservation,'joueur']);
} else {
    // Vérifier si $j_2 est vide
    if (empty($j_2)) {
        $sql = "INSERT INTO inscrits (id_user,id_reservation,role) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
    
        $result = $stmt->execute([$id_user_j1,$id_reservation,'joueur']);

        $sql = "INSERT INTO inscrits (id_user,id_reservation,role) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
    
        $result = $stmt->execute([$id_user_j3,$id_reservation,'joueur']);
        echo "j-2 est vide";
    } else {
        // Vérifier si $j_1 est vide
        if (empty($j_1)) {
            echo "j-1 est vide";
            $sql = "INSERT INTO inscrits (id_user,id_reservation,role) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute([$id_user_j2,$id_reservation,'joueur']);

            $sql = "INSERT INTO inscrits (id_user,id_reservation,role) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
        
            
        
            $result = $stmt->execute([$id_user_j3,$id_reservation,'joueur']);
        } else {
            // Aucune des variables n'est vide

            $sql = "INSERT INTO inscrits (id_user,id_reservation,role) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
        
            $result = $stmt->execute([$id_user_j1,$id_reservation,'joueur']);

            $sql = "INSERT INTO inscrits (id_user,id_reservation,role) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $result = $stmt->execute([$id_user_j2,$id_reservation,'joueur']);
            


            $sql = "INSERT INTO inscrits (id_user,id_reservation,role) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
        
            $result = $stmt->execute([$id_user_j3,$id_reservation,'joueur']);
            echo "Aucune des variables n'est vide";
        }
    }
}

?>