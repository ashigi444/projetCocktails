<?php
require_once('utils/utils.php');

/**
 * Fonctions de gestion des recettes favorites
 * La session est la source de verite, le fichier sert a la persistance
 */

/**
 * Recupere les favoris de l'utilisateur (toujours depuis la session)
 *
 * @return array Tableau des IDs de recettes favorites
 */
function getFavorites() {
    if (isset($_SESSION['favoriteRecipes'])) {
        return $_SESSION['favoriteRecipes'];
    }
    return array();
}

/**
 * Verifie si une recette est dans les favoris
 *
 * @param int $recipeId ID de la recette
 * @return bool True si la recette est favorite
 */
function isFavorite($recipeId) {
    $favorites = getFavorites();
    return in_array($recipeId, $favorites);
}

/**
 * Ajoute une recette aux favoris
 *
 * @param int $recipeId ID de la recette
 * @param string|null $username Nom d'utilisateur si connecte (pour sauvegarde fichier)
 * @return bool True si l'ajout a reussi
 */
function addFavorite($recipeId, $username = null) {
    $favorites = getFavorites();

    if (!in_array($recipeId, $favorites)) {
        $favorites[] = $recipeId;
    }

    return saveFavorites($favorites, $username);
}

/**
 * Retire une recette des favoris
 *
 * @param int $recipeId ID de la recette
 * @param string|null $username Nom d'utilisateur si connecte (pour sauvegarde fichier)
 * @return bool True si la suppression a reussi
 */
function removeFavorite($recipeId, $username = null) {
    $favorites = getFavorites();

    $key = array_search($recipeId, $favorites);
    if ($key !== false) {
        unset($favorites[$key]);
        $favorites = array_values($favorites); // Reindexter le tableau
    }

    return saveFavorites($favorites, $username);
}

/**
 * Toggle le statut favori d'une recette
 *
 * @param int $recipeId ID de la recette
 * @param string|null $username Nom d'utilisateur si connecte (pour sauvegarde fichier)
 * @return bool Nouveau statut (true si maintenant favori)
 */
function toggleFavorite($recipeId, $username = null) {
    if (isFavorite($recipeId)) {
        removeFavorite($recipeId, $username);
        return false;
    } else {
        addFavorite($recipeId, $username);
        return true;
    }
}

/**
 * Sauvegarde les favoris (en session et eventuellement fichier)
 *
 * @param array $favorites Tableau des IDs de recettes favorites
 * @param string|null $username Nom d'utilisateur si connecte
 * @return bool True si la sauvegarde a reussi
 */
function saveFavorites($favorites, $username = null) {
    // Toujours sauvegarder en session (source de verite)
    $_SESSION['favoriteRecipes'] = $favorites;

    // Si utilisateur connecte, sauvegarder aussi dans le fichier
    if ($username !== null) {
        return saveFavoritesToFile($favorites, $username);
    }

    return true;
}

/**
 * Sauvegarde les favoris dans le fichier utilisateur
 *
 * @param array $favorites Tableau des IDs de recettes favorites
 * @param string $username Nom d'utilisateur
 * @return bool True si la sauvegarde a reussi
 */
function saveFavoritesToFile($favorites, $username) {
    $filename = makeFilenameUser($username);

    if (!file_exists($filename)) {
        return false;
    }

    // Charger les donnees existantes avec file_get_contents pour eviter le cache
    $content = file_get_contents($filename);

    // Extraire $infosUser du fichier
    $tempFile = tempnam(sys_get_temp_dir(), 'user_');
    file_put_contents($tempFile, $content);
    include $tempFile;
    unlink($tempFile);

    if (!isset($infosUser)) {
        return false;
    }

    // Mettre a jour les favoris
    $infosUser['favoriteRecipes'] = $favorites;

    // Sauvegarder le fichier
    $newContent = "<?php\n\$infosUser = " . var_export($infosUser, true) . ";\n?>";
    return file_put_contents($filename, $newContent) !== false;
}

/**
 * Charge les favoris depuis le fichier utilisateur vers la session
 * Appele lors de la connexion
 *
 * @param string $username Nom d'utilisateur
 * @return array soit la liste de recettes, soit une liste vide
 */
function loadFavoritesFromFile($username) {
    $filename=makeFilenameUser($username);

    if (file_exists($filename)) {
        // Charger avec file_get_contents pour eviter le cache
        $content = file_get_contents($filename);

        // Extraire $infosUser du fichier
        $tempFile = tempnam(sys_get_temp_dir(), 'user_temp_');
        file_put_contents($tempFile, $content);
        include $tempFile;
        unlink($tempFile);

        if (isset($infosUser['favoriteRecipes'])) {
            // Fusionner avec les favoris de session existants
            $sessionFavorites = isset($_SESSION['favoriteRecipes']) ? $_SESSION['favoriteRecipes'] : array();
            $fileFavorites = $infosUser['favoriteRecipes'];

            // Union des deux tableaux (sans doublons)
            $merged = array_unique(array_merge($sessionFavorites, $fileFavorites));
            return array_values($merged);
        }
    }
    return [];
}
?>
