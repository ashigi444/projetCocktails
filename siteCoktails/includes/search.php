<?php
?>
<h2>Recherche</h2>

<?php
require_once 'resources/Donnees.inc.php';
require_once 'utils/utilsSearch.php';

if ($search !== '') {
    echo '<p>Votre requ&ecirc;te&nbsp;:&nbsp;&quot;<strong>' . replaceSearchByEntity("text", $search) . '</strong>&quot;</p>';
    $parsed = parseSearchQuery($search);

    if ($parsed['error'] !== null) {
        echo '<div class="message message-errors"><p>' . $parsed['error'] . '</p></div>';
    } else {
        $wantedRecognized = array();
        $unwantedRecognized = array();
        $notRecognized = array();
        foreach ($parsed['wanted'] as $ingredient) {
            if (ingredientExistsInHierarchy($ingredient, $Hierarchie)) {
                $wantedRecognized[] = getExactIngredientName($ingredient, $Hierarchie);
            } else {
                $notRecognized[] = $ingredient;
            }
        }
        foreach ($parsed['unwanted'] as $ingredient) {
            if (ingredientExistsInHierarchy($ingredient, $Hierarchie)) {
                $unwantedRecognized[] = getExactIngredientName($ingredient, $Hierarchie);
            } else {
                $notRecognized[] = $ingredient;
            }
        }

        echo '<div class="search-analysis">';

        if (count($wantedRecognized) > 0) {
            echo '<p><strong>Liste des aliments souhait&eacute;s&nbsp;:</strong> ' . htmlspecialchars(implode(', ', $wantedRecognized)) . '</p>';
        }

        if (count($unwantedRecognized) > 0) {
            echo '<p><strong>Liste des aliments non souhait&eacute;s&nbsp;:</strong> ' . htmlspecialchars(implode(', ', $unwantedRecognized)) . '</p>';
        }

        if (count($notRecognized) > 0) {
            echo '<p><strong>&Eacute;l&eacute;ments non reconnus dans la requ&ecirc;te&nbsp;:</strong> ' . htmlspecialchars(implode(', ', $notRecognized)) . '</p>';
        }

        echo '</div>';
        if (count($wantedRecognized) === 0 && count($unwantedRecognized) === 0) {
            echo '<div class="message message-errors"><p>Probl&egrave;me dans votre requ&ecirc;te&nbsp;: recherche impossible</p></div>';
        } else {
            $totalCriteres = count($wantedRecognized) + count($unwantedRecognized);
            $isApproximate = $totalCriteres >= 2;

            $results = array();

            foreach ($Recettes as $id => $recette) {
                $score = 0;
                foreach ($wantedRecognized as $ingredient) {
                    if (recipeContainsIngredient($recette, $ingredient, $Hierarchie)) {
                        $score++;
                    }
                }

                foreach ($unwantedRecognized as $ingredient) {
                    if (!recipeContainsIngredient($recette, $ingredient, $Hierarchie)) {
                        $score++;
                    }
                }
                $percentage = ($score / $totalCriteres) * 100;

                if ($score > 0) {
                    $results[] = array(
                        'id' => $id,
                        'recette' => $recette,
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

            echo '<div class="search-results-count">';
            echo '<p><strong>' . $fullMatches . '</strong> recette(s) satisfont enti&egrave;rement la recherche.</p>';
            if ($isApproximate && $partialMatches > 0) {
                echo '<p><strong>' . $partialMatches . '</strong> recette(s) satisfont partiellement la recherche.</p>';
            }
            echo '</div>';

            if (count($results) > 0) {
                echo '<h3>Liste des cocktails</h3>';
                echo '<div class="liste-recettes">';

                foreach ($results as $res) {
                    $id = $res['id'];
                    $recette = $res['recette'];
                    $percentage = $res['percentage'];

                    $imageName = makeFilenameImage($recette['titre']);
                    $cheminImage = 'resources/Photos/' . $imageName;

                    if (!file_exists($cheminImage)) {
                        $cheminImage = 'resources/Photos/default.jpg';
                    }

                    // verif pour favori
                    $estFavori = isFavorite($id);
                    $heartClass = $estFavori ? 'heart-full' : 'heart-empty';
                    $heartSymbol = $estFavori ? '&#10084;' : '&#9825;';

                    $toggleUrl = 'index.php?action=toggleFavorite&recipeId=' . $id . '&page=search&search=' . urlencode($search);
                    ?>
                    <?php $detailUrl = 'index.php?page=recipeDetail&recipeId=' . $id; ?>
                    <div class="cocktail-card">
                        <div class="card-header">
                            <a href="<?php echo $detailUrl; ?>" class="cocktail-title"><?php echo htmlspecialchars($recette['titre']); ?></a>
                            <a href="<?php echo $toggleUrl; ?>" class="favorite-btn <?php echo $heartClass; ?>" title="<?php echo $estFavori ? 'Retirer des favoris' : 'Ajouter aux favoris'; ?>">
                                <?php echo $heartSymbol; ?>
                            </a>
                        </div>
                        <div class="card-score">
                            Score&nbsp;: <?php echo round($percentage); ?>%
                        </div>
                        <div class="card-image">
                            <img src="<?php echo $cheminImage; ?>" alt="<?php echo htmlspecialchars($recette['titre']); ?>">
                        </div>
                        <ul class="ingredients-list">
                            <?php
                            foreach ($recette['index'] as $ing) {
                                echo "<li>" . htmlspecialchars($ing) . "</li>";
                            }
                            ?>
                        </ul>
                    </div>
                    <?php
                }
                echo '</div>';
            }
        }
    }
} else {
    ?>
    <p>Saisissez une requ&ecirc;te dans la barre de recherche en haut.</p>
    <div class="search-help">
        <h3>Aide pour la recherche</h3>
        <ul>
            <li><strong>Aliment simple&nbsp;:</strong> tapez le nom de l'aliment (ex: <code>Vodka</code>)</li>
            <li><strong>Aliment compos&eacute;&nbsp;:</strong> utilisez des guillemets (ex: <code>"Jus de fruits"</code>)</li>
            <li><strong>Aliment souhait&eacute;&nbsp;:</strong> utilisez <code>+</code> ou rien (ex: <code>+Citron</code> ou <code>Citron</code>)</li>
            <li><strong>Aliment non souhait&eacute;&nbsp;:</strong> utilisez <code>-</code> avec un espace avant (ex: <code> -Whisky</code>)</li>
        </ul>
        <p><strong>Exemple&nbsp;:</strong> <code>"Jus de fruits" +Sel -Whisky</code></p>
    </div>
    <?php
}
?>
