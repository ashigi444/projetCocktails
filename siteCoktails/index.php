<?php
session_start();
$messages = [];

//pour l'instant, on connecte juste l'utilisateur sans verif de fichier etc
if (isset($_POST['action'])) {
    if ($_POST['action'] === 'login') {
        $login = isset($_POST['login']) ? trim($_POST['login']) : '';
        if ($login !== '') {
            $_SESSION['user'] = ['login' => $login];
            $messages[] = "Connecté en tant que ".$_SESSION['user']['login'];
        } else {
            $messages[] = "Login vide : connexion impossible.";
        }
    } elseif ($_POST['action'] === 'logout') {
        unset($_SESSION['user']);
        $messages[] = "Vous êtes déconnecté.";
    }
}

// selection d'utilisateur si connecté
$user = isset($_SESSION['user']) ? $_SESSION['user'] : null;

// selection de page
$page = isset($_GET['page']) ? $_GET['page'] : 'navigation';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Gestion de Cocktails</title>
    <link rel="stylesheet" type="text/css" href="css/style.css" />
</head>
<body>

<!-- inclusion du main et du nav-->
<?php
include 'includes/header.php';
include 'includes/nav.php';
?>

<main>
<!-- contenu principal -->
<?php if (!empty($messages)) { ?>
    <div style="background:#eef; border:1px solid #99c; padding:8px; margin-bottom:10px;">
        <?php foreach ($messages as $m) { ?>
            <p><?php echo htmlspecialchars($m); ?></p>
        <?php }?>
    </div>
<?php } ?>

<?php if ($page === 'navigation'){ ?>
    <h2>Navigation</h2>
    <p>Ici, on affichera la navigation dans les recettes etc</p>

<?php } elseif ($page === 'recettesFavorites') { ?>
    <h2>Recettes pr&eacute;f&eacute;r&eacute;es</h2>
    <p>ici on affichera les recettes favorites.</p>

<?php } elseif ($page === 'recherche') { ?>
    <h2>Recherche</h2>
    <?php
    $q = isset($_GET['q']) ? $_GET['q'] : '';
    if ($q !== '') { ?>
        <p>Votre requ&ecirc;te&nbsp;:&nbsp;&quot;<strong><?php echo htmlspecialchars($q); ?></strong>&quot;</p>
        <p>Traitement de la requ&ecirc;te&nbsp;&agrave;&nbsp;impl&eacute;menter</p>
    <?php } else { ?>
        <p>Saisissez une requ&ecirc;te dans la barre de recherche en haut.</p>
    <?php } ?>

<?php } elseif ($page === 'profil') { ?>
    <h2>Profil</h2>
    <?php if (isset($_SESSION['user'])){ ?>
        <p>Connect&eacute;&nbsp;:&nbsp;<strong><?php echo htmlspecialchars($_SESSION['user']['login']); ?></strong></p>
        <p>(form de modif de profil&nbsp;&agrave;&nbsp;impl&eacute;menter)</p>
    <?php }else { ?>
        <p>Vous n&apos;&ecirc;tes pas connect&eacute;.</p>
    <?php } ?>

<?php } elseif ($page === 'inscription') { ?>
    <h2>Inscription</h2>
    <p>Form d&apos;inscription et enregistrement dans un fichier&nbsp;&agrave;&nbsp;impl&eacute;menter</p>

<?php } else { ?>
    <h2>Page inconnue</h2>
    <p>La page demand&eacute;e n&apos;existe pas.</p>
<?php } ?>
</main>

<!--inclusion du pied de page-->
<?php
include 'includes/footer.php';
?>

</body>
</html>
