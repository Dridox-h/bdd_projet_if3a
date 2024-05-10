<?php
    session_start();
    $_SESSION["id_user"] = null;
    header("Location: index.php");
    exit();
?>
<html>
<header>
    <meta charset="UTF-8">
    <title>Deconnexion</title>
    <link rel="stylesheet" href="./stylesheet/styles.css"></header>
<body>
    <h1>
      Veuillez patienter, vous allez être bientôt redirigé.
    </h1>
</body>
</html>
