<h2>Recettes pr&eacute;f&eacute;r&eacute;es</h2>

<?php if (isset($user) && isset($user['username'])) { ?> <!-- Si il y a un utilisateur connecte -->
    <!-- Si il y a au moins une recette favorites associee a cet utilisateur, alors on la/les affiche(nt)-->
    <?php if(isset($user['favoriteRecipes']) && count($user['favoriteRecipes']) > 0) { ?>
        <p>ici on affichera les recettes favorites.</p>
        <!-- TODO (traitement de l'affichage des recettes favorites) -->
    <?php }else{ ?> <!-- Sinon (c'est qu'il n'y en a pas), alors on affiche ce message -->
        <p>Vous n&apos;avez aucune recettes dans vos favoris pour le moment...</p>
        <p>Cliquez-<a href="index.php?page=navigation">ici</a> pour en ajouter...</p>
    <?php } ?>
<?php } else { ?> <!-- Sinon (c'est que l'utilisateur n'est pas connecte), alors on affiche ce message -->
    <p>Veuillez vous connecter ou cr&eacute;er un compte pour acc&eacute;der aux recettes favorites.</p>
<?php } ?>