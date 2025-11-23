<?php
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


function ingredientExistsInHierarchy($ingredient, $hierarchie) {
    foreach (array_keys($hierarchie) as $key) {
        if (strcasecmp($key, $ingredient) === 0) {
            return true;
        }
    }
    return false;
}
function getExactIngredientName($ingredient, $hierarchie) {
    foreach (array_keys($hierarchie) as $key) {
        if (strcasecmp($key, $ingredient) === 0) {
            return $key;
        }
    }
    return $ingredient;
}


function recipeContainsIngredient($recette, $ingredient, $hierarchie) {

    $validIngredients = getIngredientsHierarchy($ingredient, $hierarchie);

    foreach ($recette['index'] as $ing) {
        foreach ($validIngredients as $validIngredient) {
            if (strcasecmp($ing, $validIngredient) === 0) {
                return true;
            }
        }
    }
    return false;
}

?>

