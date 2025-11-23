<?php
?>
<h2>Recherche</h2>

<?php
require_once('resources/Donnees.inc.php');
require_once('utils/utils.php');

// parsing recherche
function parseSearchQuery($query) {
    $result = array(
        'wanted' => array(),      
        'unwanted' => array(),    
        'error' => null
    );

    $quoteCount = substr_count($query, '"');
    if ($quoteCount % 2 !== 0) {
        $result['error'] = 'Probl&egrave;me de syntaxe dans votre requ&ecirc;te&nbsp;: nombre impair de double-quotes';
        return $result;
    }
    $tokens = array();
    $currentToken = '';
    $inQuotes = false;
    $isUnwanted = false;
    $isWanted = false;

    for ($i = 0; $i < strlen($query); $i++) {
        $char = $query[$i];

        if ($char === '"') {
            $inQuotes = !$inQuotes;
            continue;
        }

        if (!$inQuotes && $char === ' ') {
            if ($currentToken !== '') {
                $tokens[] = array(
                    'value' => $currentToken,
                    'unwanted' => $isUnwanted,
                    'wanted' => $isWanted
                );
                $currentToken = '';
                $isUnwanted = false;
                $isWanted = false;
            }
            continue;
        }

        if (!$inQuotes && $char === '-' && $currentToken === '') {
            $isUnwanted = true;
            continue;
        }

        if (!$inQuotes && $char === '+' && $currentToken === '') {
            $isWanted = true;
            continue;
        }

        $currentToken .= $char;
    }
    if ($currentToken !== '') {
        $tokens[] = array(
            'value' => $currentToken,
            'unwanted' => $isUnwanted,
            'wanted' => $isWanted
        );
    }



    foreach ($tokens as $token) {
        if ($token['unwanted']) {
            $result['unwanted'][] = $token['value'];
        } else {
            $result['wanted'][] = $token['value'];
        }
    }

    return $result;
}


function alimentExistsDansHierarchie($aliment, $hierarchie) {
    foreach (array_keys($hierarchie) as $key) {
        if (strcasecmp($key, $aliment) === 0) {
            return true;
        }
    }
    return false;
}
function getNomExactAliment($aliment, $hierarchie) {
    foreach (array_keys($hierarchie) as $key) {
        if (strcasecmp($key, $aliment) === 0) {
            return $key;
        }
    }
    return $aliment;
}


function recetteContientAliment($recette, $aliment, $hierarchie) {

    $alimentsValides = getAlimentsHierarchie($aliment, $hierarchie);

    foreach ($recette['index'] as $ing) {
        foreach ($alimentsValides as $valide) {
            if (strcasecmp($ing, $valide) === 0) {
                return true;
            }
        }
    }
    return false;
}

$search = isset($_GET['search']) ? $_GET['search'] : '';

if ($search !== '') {
    echo '<p>Votre requ&ecirc;te&nbsp;:&nbsp;&quot;<strong>' . replaceSearchByEntity("text", $search) . '</strong>&quot;</p>';
    $parsed = parseSearchQuery($search);

    if ($parsed['error'] !== null) {
        echo '<div class="message message-errors"><p>' . $parsed['error'] . '</p></div>';
    } else {
        $wantedRecognized = array();
        $unwantedRecognized = array();
        $notRecognized = array();
        foreach ($parsed['wanted'] as $aliment) {
            if (alimentExistsDansHierarchie($aliment, $Hierarchie)) {
                $wantedRecognized[] = getNomExactAliment($aliment, $Hierarchie);
            } else {
                $notRecognized[] = $aliment;
            }
        }
        foreach ($parsed['unwanted'] as $aliment) {
            if (alimentExistsDansHierarchie($aliment, $Hierarchie)) {
                $unwantedRecognized[] = getNomExactAliment($aliment, $Hierarchie);
            } else {
                $notRecognized[] = $aliment;
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
                foreach ($wantedRecognized as $aliment) {
                    if (recetteContientAliment($recette, $aliment, $Hierarchie)) {
                        $score++;
                    }
                }

                foreach ($unwantedRecognized as $aliment) {
                    if (!recetteContientAliment($recette, $aliment, $Hierarchie)) {
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

                    $nomImage = makeFilenameImage($recette['titre']);
                    $cheminImage = 'resources/Photos/' . $nomImage;

                    if (!file_exists($cheminImage)) {
                        $cheminImage = 'resources/Photos/default.jpg';
                    }

                    // verif pour favori
                    $estFavori = isFavorite($id);
                    $heartClass = $estFavori ? 'heart-full' : 'heart-empty';
                    $heartSymbol = $estFavori ? '&#10084;' : '&#9825;';

                    $toggleUrl = 'index.php?action=toggleFavorite&recipeId=' . $id . '&page=search&search=' . urlencode($search);
                    ?>
                    <div class="cocktail-card">
                        <div class="card-header">
                            <span class="cocktail-title"><?php echo htmlspecialchars($recette['titre']); ?></span>
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
