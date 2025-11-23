<?php
require_once('resources/Donnees.inc.php');
require_once('utils/utils.php');

if (isset($_GET['aliment'])) {
    $currentIngredient = $_GET['aliment'];
} else {
    $currentIngredient = 'Aliment';
}

if(isset($Hierarchie)) {
    if (!array_key_exists($currentIngredient, $Hierarchie)) {
        $currentIngredient = 'Aliment';
    }
}

$validIngredients = getIngredientsHierarchy($currentIngredient, $Hierarchie);

// fil d'ariane 
if (isset($_GET['path'])) {
    $pathString = $_GET['path'];
    $breadcrumbPath = !empty($pathString) ? explode('>', $pathString) : array('Aliment');
    // ajt un aliment courant si pas deja present
    if (end($breadcrumbPath) !== $currentIngredient) {
        $breadcrumbPath[] = $currentIngredient;
    }
} else {
    $breadcrumbPath = array('Aliment');
    if ($currentIngredient !== 'Aliment') {
        $breadcrumbPath[] = $currentIngredient;
    }
}

//recup des sous-categories
$subcategories = array();
if (isset($Hierarchie[$currentIngredient]['sous-categorie'])) {
    $subcategories = $Hierarchie[$currentIngredient]['sous-categorie'];
}
?>

<div class="navigation-container">
    <div class="navigation-sidebar">
        <h3>Aliment courant</h3>

        <div class="breadcrumb-path">
            <?php
            $pathSoFar = array();

            foreach ($breadcrumbPath as $index => $aliment) {
                $pathSoFar[] = $aliment;
                $pathParam = implode('>', array_slice($pathSoFar, 0, -1));

                $isCurrent=($aliment == $currentIngredient);

                // URL pour les éléments non courants
                $linkUrl='index.php?page=navigation&aliment=' . urlencode($aliment);
                if(!empty($pathParam)){
                    $linkUrl.='&path='.urlencode($pathParam);
                }
                ?>
                <?php if($index>0){ ?>
                    <span class="separator">/</span>
                <?php } ?>

                <?php if ($isCurrent) { ?>
                    <span class="p-breadcrumb-path"><strong><?php echo $aliment; ?></strong></span>
                <?php }else{ ?>
                    <a class="a-breadcrumb-path" href="<?php echo $linkUrl; ?>"><?php echo $aliment; ?></a>
                <?php } ?>
            <?php } ?>
        </div>

        <?php
        $currentPath = implode('>', $breadcrumbPath);
        if (count($subcategories) > 0) { ?>
            <h4>Sous-cat&eacute;gories&nbsp;:</h4>
            <ul class="subcategory">
                <?php
                foreach($subcategories as $subcat){
                    $linkUrl='index.php?page=navigation&aliment=' . urlencode($subcat) . '&path=' . urlencode($currentPath);
                    ?>
                    <li><a href="<?php echo $linkUrl; ?>"><?php echo $subcat; ?></a></li>
                <?php } ?>
            </ul>
        <?php }else{ ?>
            <p class="no-subcategory">Pas de sous-cat&eacute;gorie.</p>
        <?php } ?>
    </div>

    <div class="navigation-recipes">
        <h3>Liste des cocktails</h3>
        <?php if(isset($Recettes) && !empty($Recettes)) { ?>
            <div class="liste-recettes">
                <?php
                foreach ($Recettes as $id => $recipe) {
                    $allowPrintRecipe = false;

                    if ($currentIngredient == 'Aliment') {
                        $allowPrintRecipe = true;
                    } else {
                        foreach ($recipe['index'] as $ing) {
                            foreach ($validIngredients as $validIngredient) {
                                if ($ing == $validIngredient) {
                                    $allowPrintRecipe = true;
                                    break;
                                }
                            }
                            if ($allowPrintRecipe) {
                                break;
                            }
                        }
                    }

                    if ($allowPrintRecipe) {
                        $imageName = makeFilenameImage($recipe['titre']);
                        $imagePath = 'resources/Photos/'.$imageName;

                        if (!file_exists($imagePath)) {
                            $imagePath = 'resources/Photos/default.jpg';
                        }

                        // verif pour favori
                        $isFavorite = isFavorite($id);
                        $heartClass = $isFavorite ? 'heart-full' : 'heart-empty';
                        $heartSymbol = $isFavorite ? '&#10084;' /*coeur plein*/ : '&#9825;'; /*coeur vide*/

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
                                <a href="<?php echo $detailUrl; ?>" class="cocktail-title"><?php echo htmlspecialchars($recipe['titre']); ?></a>
                                <a href="<?php echo $toggleUrl; ?>" class="favorite-btn <?php echo $heartClass; ?>" title="<?php echo $isFavorite ? 'Retirer des favoris' : 'Ajouter aux favoris'; ?>">
                                    <?php echo $heartSymbol; ?>
                                </a>
                            </div>
                            <div class="card-image">
                                <img src="<?php echo $imagePath; ?>" alt="<?php echo $recipe['titre']; ?>">
                            </div>
                            <ul class="ingredients-list">
                                <?php foreach($recipe['index'] as $ing) { ?>
                                    <li>
                                        <?php echo $ing; ?>
                                    </li>
                                <?php } ?>
                            </ul>
                        </div>
                        <?php
                    }
                } ?>
            </div>
        <?php }else{ ?>
            <p>Rien&nbsp;&agrave;&nbsp;voir ici pour le moment...</p>
        <?php } ?>
    </div>
</div>
