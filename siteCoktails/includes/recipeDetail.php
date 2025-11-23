<?php
require_once('resources/Donnees.inc.php');
require_once('utils/utils.php');
require_once('utils/utilsFavorites.php');

// recuperer l'ID de la recette
$recipeId = isset($_GET['recipeId']) ? intval($_GET['recipeId']) : null;

if ($recipeId === null || !isset($Recettes[$recipeId])) {
    ?>
    <h2>Recette introuvable</h2>
    <p>La recette demand&eacute;e n&apos;existe pas.</p>
    <p><a href="index.php?page=navigation">Retour &agrave; la navigation</a></p>
    <?php
} else {
    $recipe = $Recettes[$recipeId];

    // preparer l'image
    $imageName = makeFilenameImage($recipe['titre']);
    $imagePath = 'resources/Photos/' . $imageName;
    if (!file_exists($imagePath)) {
        $imagePath = 'resources/Photos/default.jpg';
    }

    //verifier si lee statut est favori
    $estFavori = isFavorite($recipeId);
    $heartClass = $estFavori ? 'heart-full' : 'heart-empty';
    $heartSymbol = $estFavori ? '&#10084;' : '&#9825;';
    $toggleUrl = 'index.php?action=toggleFavorite&recipeId=' . $recipeId . '&page=recipeDetail';

    //parser les ingredients
    $ingredientsDetail = explode('|', $recipe['ingredients']);
    ?>

    <div class="recipe-detail">
        <div class="recipe-header">
            <h2><?php echo htmlspecialchars($recipe['titre']); ?></h2>
            <a href="<?php echo $toggleUrl; ?>" class="favorite-btn <?php echo $heartClass; ?>" title="<?php echo $estFavori ? 'Retirer des favoris' : 'Ajouter aux favoris'; ?>">
                <?php echo $heartSymbol; ?>
            </a>
        </div>

        <div class="recipe-content">
            <div class="recipe-image">
                <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($recipe['titre']); ?>">
            </div>

            <div class="recipe-info">
                <h3>Ingr&eacute;dients</h3>
                <ul class="ingredients-detail-list">
                    <?php foreach ($ingredientsDetail as $ingredient) { ?>
                        <li><?php echo htmlspecialchars(trim($ingredient)); ?></li>
                    <?php } ?>
                </ul>

                <h3>Pr&eacute;paration</h3>
                <p class="recipe-preparation"><?php echo htmlspecialchars($recipe['preparation']); ?></p>
            </div>
        </div>

        <p class="back-link"><a href="index.php?page=navigation">&larr; Retour &agrave; la navigation</a></p>
    </div>
    <?php
}
?>
