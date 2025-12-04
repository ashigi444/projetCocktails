<?php
?>
<h2>Recherche</h2>

<?php
require_once 'resources/Donnees.inc.php';
require_once 'utils/utilsSearch.php';

// Affichage de la requete faite dans la barre de recherche
if (isset($searchValue) && !empty(trim($searchValue))) { ?>
    <p>
        Votre requ&ecirc;te&nbsp;:&nbsp;&quot;<strong>
            <?php echo replaceSearchByEntity("text", $searchValue); ?>
        </strong>&quot;
    </p>

    <?php
    $parsed = parseSearchQuery($searchValue);

    if ($parsed['error'] !== null) { ?>
        <div class="message message-errors">
            <p> <?php echo $parsed['error']; ?></p>
        </div>
    <?php } else {
        // Initialisation des listes d'aliments reconnus / non reconnus
        $wantedRecognized = array();
        $unwantedRecognized = array();
        $notRecognized = array();
        // Parcours des aliments souhaites pour verifier leur existence dans la hierarchie
        foreach ($parsed['wanted'] as $ingredient) {
            if (isset($Hierarchie) && ingredientExistsInHierarchy($ingredient, $Hierarchie)) {
                $wantedRecognized[] = getExactIngredientName($ingredient, $Hierarchie);
            } else {
                $notRecognized[] = $ingredient;
            }
        }
        // Parcours des aliments non souhaites pour verifier leur existence dans la hierarchie
        foreach ($parsed['unwanted'] as $ingredient) {
            if (ingredientExistsInHierarchy($ingredient, $Hierarchie)) {
                $unwantedRecognized[] = getExactIngredientName($ingredient, $Hierarchie);
            } else {
                $notRecognized[] = $ingredient;
            }
        } ?>

        <div class="search-analysis">
            <?php if (count($wantedRecognized) > 0) { ?>
              <!--- Affichage de la liste des aliments souhaites incluant des separateurs -->
                <p>
                    <strong>Liste des aliments souhait&eacute;s&nbsp;:&nbsp;</strong>
                    <?php echo implode(', ', $wantedRecognized); ?>
                </p>
            <?php }

            if (count($unwantedRecognized) > 0) { ?>
              <!--- Affichage de la liste des aliments non souhaites incluant des separateurs egalement -->
                <p>
                    <strong>Liste des aliments non souhait&eacute;s&nbsp;:&nbsp;</strong>
                    <?php echo implode(', ', $unwantedRecognized); ?>
                </p>
            <?php }
            // si des éléments ne sont pas reconnus, on les affiche avec un message precisant ce qui ne va pas
            if (count($notRecognized) > 0) { ?>
                <p>
                    <strong>&Eacute;l&eacute;ments non reconnus dans la requ&ecirc;te&nbsp;:&nbsp;</strong>
                    <?php echo implode(', ', $notRecognized); ?>
                </p>
            <?php } ?>
        </div>
        <!-- si aucune recette ne correspond, on affiche un message d'erreur qui nous indique qua la recherche n'a pas pu aboutir -->
        <?php if (count($wantedRecognized) === 0 && count($unwantedRecognized) === 0) { ?>
            <div class="message message-errors">
                <p>Probl&egrave;me dans votre requ&ecirc;te&nbsp;:&nbsp;recherche impossible</p>
            </div>
        <!-- dans le cas ou la recette existe, on peut faire la recherche -->
        <?php } else {
            $totaCriteria = count($wantedRecognized) + count($unwantedRecognized);
            $isApproximate = $totaCriteria >= 2;

            // Tableau des resultats trouves avec leur score
            $results = array();

            if(isset($Recettes) && !empty($Recettes)) {
                foreach ($Recettes as $id => $recipe) {
                    $score = 0;
                    // Incrementation du score si la recette contient les aliments souhaites
                    foreach ($wantedRecognized as $ingredient) {
                        if (recipeContainsIngredient($recipe, $ingredient, $Hierarchie)) {
                            $score++;
                        }
                    }
                    // Incrementation du score si la recette ne contient pas les aliments non souhaites
                    foreach ($unwantedRecognized as $ingredient) {
                        if (!recipeContainsIngredient($recipe, $ingredient, $Hierarchie)) {
                            $score++;
                        }
                    }
                    $percentage = ($score / $totaCriteria) * 100;

                    // On ne garde que les recettes qui satisfont au moins un critere
                    if ($score > 0) {
                        $results[] = array(
                                'id' => $id,
                                'recette' => $recipe,
                                'score' => $score,
                                'percentage' => $percentage
                        );
                    }
                }

                //tri
                usort($results, function ($a, $b) {
                    return $b['percentage'] - $a['percentage'];
                });

                $fullMatches = 0;
                $partialMatches = 0;
                foreach ($results as $res) {
                    if ($res['percentage'] == 100) {
                        $fullMatches++;
                    } else {
                        $partialMatches++;
                    }
                }
            } ?>

            <div class="search-results-count">
              <!-- On affiche le nombre de recettes qui satisfont la recherche -->
                <p>
                    <strong><?php echo $fullMatches; ?></strong>
                    recette(s) satisfont enti&egrave;rement la recherche.
                </p>
                <?php if ($isApproximate && $partialMatches > 0) { ?>
                  <!-- On affiche le nombre de recettes qui satisfont partiellement la recherche -->
                    <p>
                        <strong><?php echo $partialMatches; ?></strong>
                        recette(s) satisfont partiellement la recherche.
                    </p>
                <?php } ?>
            </div>

            <?php if (count($results) > 0) { ?>
                <h3>Liste des cocktails</h3>
                <div class="liste-recettes">
                    <?php foreach ($results as $res) {
                        $id = $res['id'];
                        $recipe = $res['recette'];
                        $percentage = $res['percentage'];

                        // construction du chemin vers l'image correspondant au titre de la recette
                        $imageName = makeFilenameImage($recipe['titre']);
                        $imagePath = 'resources/Photos/' . $imageName;

                        // verifier si l'image existe, sinon une image par defaut est mise en place
                        if (!file_exists($imagePath)) {
                            $imagePath = 'resources/Photos/default.jpg';
                        }

                        // verif pour favori
                        $isFavorites = isFavorite($id);
                        $heartClass = $isFavorites ? 'heart-full' : 'heart-empty';
                        $heartSymbol = $isFavorites ? '&#10084;' : '&#9825;';

                        $toggleUrl = 'index.php?toggleFavorite=true&recipeId=' . $id . '&search=Rechercher&searchValue=' . urlencode($searchValue);
                        ?>
                        <?php $detailUrl = 'index.php?page=recipeDetail&recipeId=' . $id; ?>
                        <div class="cocktail-card">
                            <div class="card-header">
                                <a href="<?php echo $detailUrl; ?>" class="cocktail-title"><?php echo $recipe['titre']; ?></a>
                                <a href="<?php echo $toggleUrl; ?>" class="favorite-btn <?php echo $heartClass; ?>" title="<?php echo $isFavorites ? 'Retirer des favoris' : 'Ajouter aux favoris'; ?>">
                                    <?php echo $heartSymbol; ?>
                                </a>
                            </div>
                            <div class="card-score">
                                Score&nbsp;:&nbsp;<?php echo round($percentage); ?>%
                            </div>
                            <div class="card-image">
                                <img src="<?php echo $imagePath; ?>" alt="<?php echo $recipe['titre']; ?>">
                            </div>
                            <ul class="ingredients-list">
                                <?php
                                // Affichage de la liste des ingredients utilises dans la recette
                                foreach ($recipe['index'] as $ing) { ?>
                                    <li><?php echo $ing; ?></li>
                                <?php } ?>
                            </ul>
                        </div>
                        <?php
                    } ?>
                </div>
            <?php }
        }
    }
} else { ?>
    <p>Saisissez une requ&ecirc;te dans la barre de recherche en haut.</p>
    <div class="search-help">
      <!-- on affiche une petite aide pour la recherche -->
        <h3>Aide pour la recherche</h3>
        <ul>
            <li><strong>Aliment simple&nbsp;:&nbsp;</strong>tapez le nom de l&apos;aliment&nbsp;(ex&nbsp;:&nbsp;<code>Vodka</code>)</li>
            <li><strong>Aliment compos&eacute;&nbsp;:&nbsp;</strong>utilisez des guillemets&nbsp;(ex&nbsp;:&nbsp;<code>&quot;Jus de fruits&quot;</code>)</li>
            <li><strong>Aliment souhait&eacute;&nbsp;:&nbsp;</strong>utilisez <code>+</code> ou rien&nbsp;(ex&nbsp;:&nbsp;<code>+Citron</code> ou <code>Citron</code>)</li>
            <li><strong>Aliment non souhait&eacute;&nbsp;:&nbsp;</strong>utilisez <code>-</code> avec un espace avant&nbsp;(ex&nbsp;:&nbsp;<code>-Whisky</code>)</li>
        </ul>
        <!-- avec un exemple de recherche reprenant les aides precedemment annoncees -->
        <p><strong>Exemple&nbsp;:</strong> <code>&quot;Jus de fruits&quot;&nbsp;+Sel&nbsp;-Whisky</code></p>
    </div>
<?php } ?>
