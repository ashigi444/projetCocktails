<?php
require_once('resources/Donnees.inc.php');
require_once('utils/utils.php');

if (isset($_GET['aliment'])) {
    $currentIngredient = $_GET['aliment'];
} else {
    $currentIngredient = 'Aliment';
}

if (!array_key_exists($currentIngredient, $Hierarchie)) {
    $currentIngredient = 'Aliment';
}

$ingredientsValides = getIngredientsHierarchy($currentIngredient, $Hierarchie);

// fil d'ariane 
if (isset($_GET['path'])) {
    $pathString = $_GET['path'];
    $breadcrumb = !empty($pathString) ? explode('>', $pathString) : array('Aliment');
    // ajt un aliment courant si pas deja present
    if (end($breadcrumb) !== $currentIngredient) {
        $breadcrumb[] = $currentIngredient;
    }
} else {
    $breadcrumb = array('Aliment');
    if ($currentIngredient !== 'Aliment') {
        $breadcrumb[] = $currentIngredient;
    }
}

//recup des sous-categories
$sousCategories = array();
if (isset($Hierarchie[$currentIngredient]['sous-categorie'])) {
    $sousCategories = $Hierarchie[$currentIngredient]['sous-categorie'];
}
?>

<div class="navigation-container">
    <div class="navigation-sidebar">
        <h3>Aliment courant</h3>

        <div class="breadcrumb">
            <?php
            $pathSoFar = array();
            foreach ($breadcrumb as $index => $aliment) {
                if ($index > 0) {
                    echo ' / ';
                }
                $pathSoFar[] = $aliment;
                $pathParam = implode('>', array_slice($pathSoFar, 0, -1));
                if ($aliment === $currentIngredient) {
                    echo '<strong>' . htmlspecialchars($aliment) . '</strong>';
                } else {
                    $linkUrl = 'index.php?page=navigation&aliment=' . urlencode($aliment);
                    if (!empty($pathParam)) {
                        $linkUrl .= '&path=' . urlencode($pathParam);
                    }
                    echo '<a href="' . $linkUrl . '">' . htmlspecialchars($aliment) . '</a>';
                }
            }
            ?>
        </div>

        <?php
        $currentPath = implode('>', $breadcrumb);
        if (count($sousCategories) > 0) { ?>
            <h4>Sous-cat&eacute;gories&nbsp;:</h4>
            <ul class="sous-categories">
                <?php
                foreach ($sousCategories as $sousCat) {
                    $linkUrl = 'index.php?page=navigation&aliment=' . urlencode($sousCat) . '&path=' . urlencode($currentPath);
                    ?>
                    <li><a href="<?php echo $linkUrl; ?>"><?php echo htmlspecialchars($sousCat); ?></a></li>
                <?php } ?>
            </ul>
        <?php } else { ?>
            <p class="no-subcategory">Pas de sous-cat&eacute;gorie.</p>
        <?php } ?>
    </div>

    <div class="navigation-recipes">
        <h3>Liste des cocktails</h3>
        <div class="liste-recettes">
    <?php
    foreach ($Recettes as $id =>$recette) {
        $afficherRecette = false;

        if ($currentIngredient == 'Aliment') {
            $afficherRecette = true;
        } else {
            foreach ($recette['index'] as $ing) {
                foreach ($ingredientsValides as $validIngredient) {
                    if ($ing == $validIngredient) {
                        $afficherRecette = true;
                        break;
                    }
                }
                if ($afficherRecette) {
                    break;
                }
            }
        }

        if ($afficherRecette) {
            $imageName = makeFilenameImage($recette['titre']);
            $cheminImage = 'resources/Photos/'.$imageName;

            if (!file_exists($cheminImage)) {
                $cheminImage = 'resources/Photos/default.jpg';
            }

            // verif pour favori
            $estFavori = isFavorite($id);
            $heartClass = $estFavori ? 'heart-full' : 'heart-empty';
            $heartSymbol = $estFavori ? '&#10084;' : '&#9825;';

            $toggleUrl = 'index.php?action=toggleFavorite&recipeId=' . $id . '&page=navigation';
            if ($currentIngredient !== 'Aliment') {
                $toggleUrl .= '&aliment=' . urlencode($currentIngredient);
            }
            if (isset($currentPath) && !empty($currentPath)) {
                $toggleUrl .= '&path=' . urlencode($currentPath);
            }

            // URL pour l'affichage detaille
            $detailUrl = 'index.php?page=recipeDetail&recipeId=' . $id;
            ?>
            <div class="cocktail-card">
                <div class="card-header">
                    <a href="<?php echo $detailUrl; ?>" class="cocktail-title"><?php echo htmlspecialchars($recette['titre']); ?></a>
                    <a href="<?php echo $toggleUrl; ?>" class="favorite-btn <?php echo $heartClass; ?>" title="<?php echo $estFavori ? 'Retirer des favoris' : 'Ajouter aux favoris'; ?>">
                        <?php echo $heartSymbol; ?>
                    </a>
                </div>
                <div class="card-image">
                    <img src="<?php echo $cheminImage; ?>" alt="<?php echo $recette['titre']; ?>">
                </div>
                <ul class="ingredients-list">
                    <?php foreach($recette['index'] as $ing) { ?>
                        <li>
                            <?php echo $ing; ?>
                        </li>
                    <?php } ?>
                </ul>
            </div>
            <?php
        }
    }
    ?>
        </div>
    </div>
</div>
