<?php 


function email_present($email, $bdd) {
    $query = $bdd->prepare("SELECT email FROM utilisateur WHERE email = :email");
    
    $query->execute(['email' => $email]);
    
    $result = $query->fetch(PDO::FETCH_ASSOC);
    
    if ($result) {
        return 0; // L'e-mail est déjà présent
    } else {
        return 1; // L'e-mail n'est pas présent
    }
}
session_start();
$id_session = session_id();


try{
    $bdd = new PDO("mysql:host=localhost;dbname=tennis;charset=utf8", "root", "");

} catch (PDOException $e) {
    die('Erreur de connexion : ' . $e->getMessage());
}

$nom = $_POST["nom"];
$prenom = $_POST["prenom"];
$email = $_POST["email"];
$password = $_POST["password"];

if(email_present($email,$bdd)==0){
    echo "emial déjà présent";
}else {
    $req = $bdd->prepare("INSERT INTO utilisateur(nom, prenom, email, password) VALUES (?,?,?,?);");
    $req->execute([$nom, $prenom,$email,$password]);
}



?>