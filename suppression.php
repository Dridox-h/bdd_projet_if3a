<?php 
include 'db_connect.php';
session_start();

$userId = $_SESSION['id_user'];
echo $_SESSION['role'];
// Vérifier si la valeur est définie et n'est pas vide
if (isset($_POST['supp_reservation']) && !empty($_POST['supp_reservation'])) {
    $id_res = $_POST['supp_reservation'];
    
    // Supprimer les données de la base de données des relations supp
    // si c'est le leader qui veut supprimer alors on supprime toutes la réservations
    if ($_SESSION['role'] = "leader"){
        echo "on est la ";
    $req = $conn->prepare("DELETE FROM inscrits WHERE id_reservation = ?");
    $req->execute([$id_res]);
    $req = $conn->prepare("DELETE FROM reservation WHERE id_reservation = ?");
    $req->execute([$id_res]);
    }// Si c'est un joeuur lambda qui veut supprimer sa réservation on enlève son inscription
    else {
        $req = $conn->prepare("DELETE FROM inscrits WHERE id_user = ?");
        $req->execute([$_SESSION['id_user']]);

    }

    // Afficher un message de succès ou de confirmation
    echo "La réservation a été supprimée avec succès.";
echo "<a href='index.php'> retour au menu </a>";
} else {
    // Si la valeur n'est pas définie ou est vide, afficher un message d'erreur
    echo "Erreur : Identifiant de réservation non spécifié.";
}
?>
