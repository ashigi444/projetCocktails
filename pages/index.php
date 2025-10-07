<?php
session_start();
$messages = [];

//pour l'instant on connecte juste l'utilisateur sans verif de fichier etc
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

// selec de page
$page = isset($_GET['page']) ? $_GET['page'] : 'navigation';

include 'header.php';
?>

<!-- contznu principal -->
<div style="max-width: 900px; margin: 16px auto; padding: 8px;">
    <?php if (!empty($messages)): ?>
        <div style="background:#eef; border:1px solid #99c; padding:8px; margin-bottom:10px;">
            <?php foreach ($messages as $m): ?>
                <div><?= htmlspecialchars($m) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($page === 'navigation'): ?>
        <h2>Navigation</h2>
        <p>Ici, on affichera la navigation dans les recettes etc</p>

    <?php elseif ($page === 'favorites'): ?>
        <h2>Recettes préférées</h2>
        <p>ici on affichera les recettes favorites.</p>

    <?php elseif ($page === 'recherche'): ?>
        <h2>Recherche</h2>
        <?php
        $q = isset($_GET['q']) ? $_GET['q'] : '';
        if ($q !== '') {
            echo '<p>Votre requête : <strong>'.htmlspecialchars($q).'</strong></p>';
            echo '<p>traitement de la requête à implémenter</p>';
        } else {
            echo '<p>Saisissez une requête dans la barre de recherche en haut.</p>';
        }
        ?>

    <?php elseif ($page === 'profil'): ?>
        <h2>Profil</h2>
        <?php if (isset($_SESSION['user'])): ?>
            <p>Connecté : <strong><?= htmlspecialchars($_SESSION['user']['login']) ?></strong></p>
            <p>(form de modif de profil à implémenter)</p>
        <?php else: ?>
            <p>Vous n'êtes pas connecté.</p>
        <?php endif; ?>

    <?php elseif ($page === 'inscription'): ?>
        <h2>Inscription</h2>
        <p>(form d'inscription et enregistrement dans un fichier à implémenter)</p>

    <?php else: ?>
        <h2>Page inconnue</h2>
        <p>La page demandée n'existe pas.</p>
    <?php endif; ?>
</div>

<?php
// inclusion du pied de page
include 'footer.php';
