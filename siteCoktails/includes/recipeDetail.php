<?php
require_once('resources/Donnees.inc.php');
require_once('utils/utils.php');
require_once('utils/utilsFavorites.php');

// recuperer l'ID de la recette
$recipeId = isset($_GET['recipeId']) ? intval($_GET['recipeId']) : null;

// verifier si la recette existe (dans ce cas on affiche un message d'erreur)
if ($recipeId === null || !isset($Recettes[$recipeId])) {
    ?>
    <h2>Recette introuvable</h2>
    <p>La recette demand&eacute;e n&apos;existe pas.</p>
    <p><a href="index.php?page=navigation">Retour &agrave; la navigation</a></p>
    <?php
    // sinon on affiche la recette avec sa photo, la possibilite de mettre en favori ou non ainsi qu'un detail des ingredients presents dans le cocktail
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
    $toggleUrl = 'index.php?toggleFavorite=true&recipeId=' . $recipeId . '&page=recipeDetail';

    //parser les ingredients
    $ingredientsDetail = explode('|', $recipe['ingredients']);
    ?>
    <!--- Section d'ajout (ou non) de la recette aux favoris -->
    <div class="recipe-detail">
        <div class="recipe-header">
            <h2><?php echo $recipe['titre']; ?></h2>
            <a href="<?php echo $toggleUrl; ?>" class="favorite-btn <?php echo $heartClass; ?>" title="<?php echo $estFavori ? 'Retirer des favoris' : 'Ajouter aux favoris'; ?>">
                <?php echo $heartSymbol; ?>
            </a>
        </div>
        <!--- Section d'affichage de l'image -->
        <div class="recipe-content">
            <div class="recipe-image">
                <img src="<?php echo $imagePath; ?>" alt="<?php echo $recipe['titre']; ?>">
            </div>
            <!--- Section d'affichage des ingredients et de la recette -->
            <div class="recipe-info">
                <h3>Ingr&eacute;dients</h3>
                <ul class="ingredients-detail-list">
                    <?php foreach ($ingredientsDetail as $ingredient) { ?>
                        <li><?php echo trim($ingredient); ?></li>
                    <?php } ?>
                </ul>
                <!--- Section d'affichage de la recette -->
                <h3>Pr&eacute;paration</h3>
                <p class="recipe-preparation"><?php echo $recipe['preparation']; ?></p>
            </div>
        </div>
        <!--- Section de retour vers la navigation -->
        <p class="back-link"><a href="index.php?page=navigation">&larr; Retour &agrave; la navigation</a></p>
    </div>
    <?php
}
?>
