<nav>
<ul>
    <li><a href="index.php">Accueil</a></li>
    <li><a href="index.php?page=favoriteRecipes">Recettes</a></li>
</ul>
<!-- formulaire de recherche -->
<form method="get" action="index.php">
    <input type="hidden" name="page" value="search">
    <input type="text" name="q" placeholder="&quot;Jus de fruits&quot;"
           value="<?php echo isset($_GET['q']) ? $_GET['q'] : '' ; ?>"
    >
    <button type="submit">Rechercher</button>
</form>

<!-- zone de connexion -->
<div>
<?php if (isset($user) && !empty($user)) { ?>
    <p><a href="index.php?page=profilSettings">Profil&nbsp;:</a>
    <strong><?php echo $user['login'] ; ?></strong></p>
<?php } ?>
<form method="post" action="index.php">
<?php if (isset($user) && !empty($user)) { ?>
    <input type="hidden" name="action" value="logout">
    <button type="submit">Se d&eacute;connecter</button>
<?php } else { ?>
    <input type="text" name="login" placeholder="Login" required="required">
    <input type="password" name="password" placeholder="Mot de passe" required="required">
    <input type="hidden" name="action" value="login">
    <button type="submit">Connexion</button>
<?php } ?>
</form>

<?php if (!isset($user) || empty($user)){ ?>
    <a href="index.php?page=signUp">S&apos;inscrire</a>
<?php } ?>
</div>
</nav>
