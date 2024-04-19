<?php 

session_start();
$id_session = session_id();


$bdd = new PDO("mysql:host=localhost;dbname=tennis;charset=utf8", "root", "");

$nom = $_POST["nom"];
$prenom = $_POST["prenom"];
$email = $_POST["email"];
$password = $_POST["password"];

$req = $bdd->prepare("INSERT INTO utilisateur(nom, prenom, email, password) VALUES (?,?,?,?);");
$req->execute([$nom, $prenom,$email,$password]);



?>