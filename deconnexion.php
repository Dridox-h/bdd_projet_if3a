<?php
    session_start();

    $_SESSION = [];

    // Verification si cookie de session, si oui, suppression du cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    session_destroy();

    header("Location: index.php");
    exit();
?>
<!DOCTYPE html>
<html lang="fr">
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