<h2>Recettes pr&eacute;f&eacute;r&eacute;es</h2>

<?php if (isset($user) && isset($user['username'])) { ?>
    <!-- TODO (traitement de l'affichage des recettes favorites) -->
    <p>ici on affichera les recettes favorites.</p>
<?php } else { ?>
    <p>Veuillez vous connecter ou cr&eacute;er un compte pour acc&eacute;der aux recettes favorites.</p>
<?php } ?>