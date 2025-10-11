<?php
session_start();
$messages = [];

// pour l'instant, on connecte juste l'utilisateur sans verifs de fichier etc
// et sans verifs via expressions régulières (=regex)
if (isset($_POST['action'])) {
    if ($_POST['action'] == 'login') {
        $login = isset($_POST['login']) ? trim($_POST['login']) : '';
        if ($login !== '') {
            $_SESSION['user'] = ['login' => $login];
            $messages[] = "Connecté en tant que ".$_SESSION['user']['login'];
        } else {
            $messages[] = "Login vide : connexion impossible.";
        }
    } elseif ($_POST['action'] == 'logout') {
        unset($_SESSION['user']);
        $messages[] = "Vous êtes déconnecté.";
    }
}

// selection d'utilisateur si connecté
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

// selection de page
if(isset($_GET['page']) && !empty(trim($_GET['page']))){
    $page = $_GET['page'];
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Gestion de Cocktails</title>
    <link rel="stylesheet" type="text/css" href="css/style.css" />
</head>
<body>

<!-- inclusion du header et du nav-->
<?php
include 'includes/header.php';
include 'includes/nav.php';

// inclusion du main
include 'includes/main.php';

// inclusion du pied de page
include 'includes/footer.php';
?>

</body>
</html>
