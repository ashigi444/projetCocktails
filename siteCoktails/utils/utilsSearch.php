<?php
require_once('utils/utils.php');

/**
 * Analyse une chaine de recherche saisie par l'utilisateur.
 * Separe les termes souhaites et non souhaites, gere les guillemets
 * et retourne une structure avec listes wanted / unwanted et un message d'erreur eventuel.
 *
 * @param string $query la chaine de recherche saisie par l'utilisateur
 * @return array tableau associatif contenant les listes 'wanted', 'unwanted' et un eventuel message d'erreur
 */
function parseSearchQuery($query) {
    // Initialisation du tableau de resultat avec listes d'aliments voulus / non voulus et eventuelle erreur
    $result = array(
        'wanted' => array(),
        'unwanted' => array(),
        'error' => null
    );

    // Comptage du nombre de guillemets pour verifier la coherence de la requete
    $quoteCount = substr_count($query, '"');
    if ($quoteCount % 2 !== 0) {
        // Si on a un nombre impair de guillemets -> erreur de syntaxe
        $result['error'] = 'Probl&egrave;me de syntaxe dans votre requ&ecirc;te&nbsp;: nombre impair de double-quotes';
        return $result;
    }

    // Tableau qui contiendra les tokens trouves dans la requete
    $tokens = array();
    // Token actuellement en cours de construction
    $currentToken = '';
    // Indique si on est a l'interieur de guillemets
    $inQuotes = false;
    // Indique si le token courant est precede d'un '-'
    $isUnwanted = false;
    // Indique si le token courant est precede d'un '+'
    $isWanted = false;

    // Parcours caractere par caractere de la requete utilisateur
    for ($i = 0; $i < strlen($query); $i++) {
        $char = $query[$i];

        // Gestion de l'ouverture / fermeture des guillemets
        if ($char === '"') {
            $inQuotes = !$inQuotes;
            continue;
        }

        // Si on n'est pas dans des guillemets et qu'on rencontre un espace
        if (!$inQuotes && $char === ' ') {
            // On finalise le token courant si non vide
            if ($currentToken !== '') {
                $tokens[] = array(
                    'value' => $currentToken,
                    'unwanted' => $isUnwanted,
                    'wanted' => $isWanted
                );
                // On reinitialise pour le prochain token
                $currentToken = '';
                $isUnwanted = false;
                $isWanted = false;
            }
            continue;
        }

        // Detection d'un '-' au debut d'un token hors guillemets => ingredient non souhaite
        if (!$inQuotes && $char === '-' && $currentToken === '') {
            $isUnwanted = true;
            continue;
        }

        // Detection d'un '+' au debut d'un token hors guillemets => ingredient souhaite
        if (!$inQuotes && $char === '+' && $currentToken === '') {
            $isWanted = true;
            continue;
        }

        // Ajout du caractere courant au token en construction
        $currentToken .= $char;
    }

    // Ajout du dernier token si il existe encore apres la boucle
    if ($currentToken !== '') {
        $tokens[] = array(
            'value' => $currentToken,
            'unwanted' => $isUnwanted,
            'wanted' => $isWanted
        );
    }

    // Classement final des tokens entre wanted et unwanted selon leurs indicateurs
    foreach ($tokens as $token) {
        if ($token['unwanted']) {
            $result['unwanted'][] = $token['value'];
        } else {
            $result['wanted'][] = $token['value'];
        }
    }

    // On renvoie la structure contenant les listes d'aliments et l'eventuelle erreur
    return $result;
}

/**
 * Verifie si un ingredient existe comme cle dans la hierarchie.
 * Parcourt la liste des aliments de la hierarchie et compare les noms.
 *
 * @param string $ingredient le nom de l'ingredient recherche
 * @param array $hierarchy la hierarchie complete des ingredients
 * @return bool true si l'ingredient existe comme cle dans la hierarchie, false sinon
 */
function ingredientExistsInHierarchy($ingredient, $hierarchy) {
    // Parcourt tous les noms d'aliments de la hierarchie
    foreach (array_keys($hierarchy) as $key) {
        // Compare avec l'ingredient recherche (comparaison stricte)
        if (strcmp($key, $ingredient) === 0) {
            return true;
        }
    }
    // Si aucun aliment ne correspond, on renvoie false
    return false;
}

/**
 * Recupere le nom exact d'un ingredient tel qu'il apparait dans la hierarchie.
 * Renvoie la cle correspondante si trouvee, sinon renvoie le nom donne en parametre.
 *
 * @param string $ingredient le nom d'ingredient fourni (potentiellement mal case)
 * @param array $hierarchy la hierarchie complete des ingredients
 * @return int|mixed|string le nom exact tel qu'il figure dans la hierarchie ou le nom d'origine si non trouve
 */
function getExactIngredientName($ingredient, $hierarchy) {
    // Parcourt les cles de la hierarchie pour retrouver la forme exacte de l'aliment
    foreach (array_keys($hierarchy) as $key) {
        if (strcmp($key, $ingredient) === 0) {
            // Retourne le nom tel qu'il existe dans la hierarchie
            return $key;
        }
    }
    // Si aucun match, on renvoie le nom d'origine
    return $ingredient;
}

/**
 * Verifie si une recette contient un ingredient donne ou une de ses sous categories.
 * Utilise la hierarchie des ingredients pour considerer l'ingredient et ses descendants.
 *
 * @param array $recipe le tableau representant la recette (avec un index des ingredients)
 * @param string $ingredient le nom de l'ingredient recherche
 * @param array $hierarchy la hierarchie complete des ingredients
 * @return bool true si la recette contient l'ingredient ou une de ses sous categories, false sinon
 */
function recipeContainsIngredient($recipe, $ingredient, $hierarchy) {

    // Recupere la liste complete des ingredients valides (ingredient + sous categories)
    $validIngredients = getIngredientsHierarchy($ingredient, $hierarchy);

    // Parcourt l'index des ingredients de la recette
    foreach ($recipe['index'] as $ing) {
        // Pour chaque ingredient de la recette, on verifie si il est dans la liste valide
        foreach ($validIngredients as $validIngredient) {
            if (strcmp($ing, $validIngredient) === 0) {
                // Si un match est trouve, la recette contient bien l'ingredient cherche
                return true;
            }
        }
    }
    // Si aucun match n'a ete trouve, on renvoie false
    return false;
}

?>
