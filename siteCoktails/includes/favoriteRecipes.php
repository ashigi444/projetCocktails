<?php
?>
    <h2>Recettes pr&eacute;f&eacute;r&eacute;es</h2>

<?php
include_once('resources/Donnees.inc.php');
require_once('utils/utilsFavorites.php');

// recup du username
$username = isset($_SESSION['user']['username']) ? $_SESSION['user']['username'] : null;

// recup les favoris
$favorites = getFavorites();

// affichage des favoris
if (count($favorites) > 0) { ?>
    <p>Vous avez <strong><?php echo count($favorites); ?></strong> recette(s) dans vos favoris.</p>
    <!-- liste des favoris -->
    <div class="liste-recettes">
        <?php
        foreach ($favorites as $recipeId) {
            if (!isset($Recettes[$recipeId])) {
                continue;
            }
            // récupération de l'image
            $recipe = $Recettes[$recipeId];
            $imageName = makeFilenameImage($recipe['titre']);
            $imagePath = 'resources/Photos/' . $imageName;

            // verifier si l'image existe
            if (!file_exists($imagePath)) {
                $imagePath = 'resources/Photos/default.jpg';
            }
            $toggleUrl = 'index.php?toggleFavorite=true&recipeId=' . $recipeId . '&page=favoriteRecipes';
            $detailUrl = 'index.php?page=recipeDetail&recipeId=' . $recipeId;
            ?>
            <!-- affichage du titre du cocktail + bouton favori -->
            <div class="cocktail-card">
                <div class="card-header">
                    <a href="<?php echo $detailUrl; ?>" class="cocktail-title"><?php echo $recipe['titre']; ?></a>
                    <a href="<?php echo $toggleUrl; ?>" class="favorite-btn heart-full" title="Retirer des favoris">
                        &#10084;
                    </a>
                </div>
                <!-- affichage de l'image -->
                <div class="card-image">
                    <img src="<?php echo $imagePath; ?>" alt="<?php echo $recipe['titre']; ?>">
                </div>
                <!-- liste des ingrédients -->
                <ul class="ingredients-list">
                    <?php foreach ($recipe['index'] as $ing) { ?>
                        <li>
                            <?php echo $ing; ?>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <?php
        }
        ?>
    </div>
<?php } else { ?>
    <p>Vous n&apos;avez pas de recettes dans vos favoris...</p>
    <p>Cliquez <a href="index.php?page=navigation">ici</a> pour parcourir les recettes et en ajouter.</p>
<?php } ?>
