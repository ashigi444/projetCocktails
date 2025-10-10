<main>
    <?php if (!empty($messages)) { ?>
        <div style="background:#eef; border:1px solid #99c; padding:8px; margin-bottom:10px;">
            <?php foreach ($messages as $mess) { ?>
                <p><?php echo $mess; ?></p>
            <?php }?>
        </div>
    <?php }

    if ($page === 'navigation'){
        include 'includes/navigation.php';
    } elseif ($page === 'recettesFavorites') {
        include 'includes/favoriteRecipes.php';
    } elseif ($page === 'recherche') {
        include 'includes/search.php';
    } elseif ($page === 'profil') {
        include 'includes/profilSettings.php';
    } elseif ($page === 'inscription') {
        include 'includes/signUp.php';
    } else { ?>
        <h2>ERREUR&nbsp;404&nbsp;:&nbsp;Page inconnue</h2>
        <p>La page demand&eacute;e n&apos;existe pas.</p>
    <?php } ?>
</main>