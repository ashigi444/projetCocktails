<?php
/**
 * Remplace les caracteres sensibles dans une valeur de recherche en fonction du mode utilise.
 * Utilise safeTextSearch pour un affichage texte et safeInputSearch pour un champ de formulaire.
 *
 * @param string $mode le mode de securisation utilise ('text' ou 'input')
 * @param string $searchValue la chaine saisie par l'utilisateur a securiser
 * @return array|mixed|string|string[] la valeur securisee adaptee au contexte ou la valeur originale si le mode est inconnu
 */
function replaceSearchByEntity($mode, $searchValue){
    // Redirige vers la fonction de securisation adaptee selon le contexte (texte ou input)
    if($mode=='text'){
        return safeTextSearch($searchValue);
    }else if($mode=='input'){
        return safeInputSearch($searchValue);
    }
    // Si le mode n'est pas reconnu, on renvoie la valeur originale
    return $searchValue;
}

/**
 * Securise une chaine destinee a etre affichee dans du texte HTML
 * en remplacant les chevrons et l'esperluette par leurs entites.
 *
 * @param string $searchValue la chaine brute saisie par l'utilisateur
 * @return array|string|string[] la chaine securisee pour un affichage dans du texte HTML
 */
function safeTextSearch($searchValue){
    // Remplacement des chevrons pour eviter l'injection HTML dans du texte
    $searchValue = str_replace('<', '&lt;', $searchValue);
    $searchValue = str_replace('>', '&gt;', $searchValue);
    // Remplacement du esperluette en dernier pour eviter les conflits
    $searchValue = str_replace('&', '&amp;', $searchValue);
    return $searchValue;
}

/**
 * Securise une chaine destinee a etre placee dans un attribut d'input HTML
 * en remplacant les guillemets par leurs entites.
 *
 * @param string $searchValue la chaine brute saisie par l'utilisateur
 * @return array|string|string[] la chaine securisee pour une utilisation dans un attribut d'input
 */
function safeInputSearch($searchValue){
    // Remplacement des guillemets pour proteger les attributs d'input
    $searchValue = str_replace('"', '&quot;', $searchValue);
    $searchValue = str_replace('\'', '&apos;', $searchValue);
    return $searchValue;
}

/**
 * Corrige et normalise un nom de fichier a partir d'une chaine d'origine.
 * Supprime les espaces en trop, translittere les accents et ne garde que lettres, chiffres et underscores.
 *
 * @param string $filenameOriginal le nom de fichier original saisi ou genere
 * @return array|string|string[]|null le nom de fichier corrige et normalise
 */
function makeFilenameCorrected($filenameOriginal) {
    // Supprime les espaces en trop au debut et a la fin
    $filename = trim($filenameOriginal);
    // Remplace tout chaque lettre accentuee par la meme lettre sans accent
    /*$filename = preg_replace('/[aAaAæÆaAaAaAaAaAeEeEeEeEeEeEeEyYuUuUuUuUuUiIiIiIiIiIiIoOœŒoOoOoOoOoOcCcCcCnNnN]+(([-']|( )*)[a-zA-ZaAaAæÆaAaAaAaAaAeEeEeEeEeEeEeEyYuUuUuUuUuUiIiIiIiIiIiIoOœŒoOoOoOoOoOcCcCcCnNnN]+)*)+$/', 'a',$filename)
    $filename = preg_replace('/[eEeEeEeEeEeEeE]/', 'e',$filename)
    $filename = preg_replace('/[yY]/', 'y',$filename)
    $filename = preg_replace('/[uUuUuUuUuU]/', 'u',$filename)
    $filename = preg_replace('/[iIiIiIiIiIiI]/', 'i',$filename)
    $filename = preg_replace('/[oOœŒoOoOoOoOoO]/', 'o',$filename)
    $filename = preg_replace('/[cCcCcC]/', 'c',$filename)
    $filename = preg_replace('/[nNnN]/', 'n',$filename)*/
    // Utilise iconv pour translitter les caracteres accentues vers ASCII
    $filename = iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $filename);
    // Remplace les espaces par des underscores
    $filename = preg_replace('/ /', '_',$filename);
    // Ne garde que lettres, chiffres et underscores
    $filename = preg_replace('/[^a-zA-Z0-9_]/', '',$filename);

    return $filename;
}

/**
 * Genere le nom de fichier d'une image de cocktail a partir de son titre.
 * Met le nom en minuscules, le corrige, puis met la premiere lettre en majuscule et ajoute .jpg.
 *
 * @param string $imageName le nom logique de l'image (ex: nom du cocktail)
 * @return string le nom de fichier image final au format "Xxxxx.jpg"
 */
function makeFilenameImage($imageName) {
    // Nettoie le nom, en minuscule puis applique makeFilenameCorrected
    $imageName=makeFilenameCorrected(strtolower($imageName)); // Met tout le nom en minusucule
    if (strlen($imageName) > 0) {
        // Force la premiere lettre en majuscule pour respecter la convention du sujet
        $imageName[0] = strtoupper($imageName[0]); // Met la premiere lettre en majuscule
    }
    return $imageName.'.jpg'; // return le nom de l'image suivi de .jpg
}

/**
 * Construit le chemin complet du fichier utilisateur associe a un username.
 * Utilise makeFilenameCorrected pour normaliser le nom du fichier.
 *
 * @param string $username le nom d'utilisateur dont on veut le fichier associe
 * @return string le chemin complet du fichier utilisateur dans dataUsers
 */
function makeFilenameUser($username){
    // Cree le chemin de fichier pour stocker les infos utilisateur
    return "dataUsers/user".makeFilenameCorrected($username).".php";
}

/**
 * Charge les informations d'un utilisateur a partir de son username.
 * Construit le nom de fichier, inclut le fichier et renvoie le tableau $infosUser si valide.
 *
 *
 * @param string $username le nom d'utilisateur dont on veut charger les informations
 * @return array|null le tableau d'informations utilisateur ou null si non disponible
 */
function loadUserInfos($username){
    // Construit le chemin du fichier utilisateur
    $filename=makeFilenameUser($username);
    // Si le fichier n'existe pas, on renvoie null
    if (!file_exists($filename)) {
        return null;
    }
    // Initialisation de la variable qui sera chargee via require
    $infosUser = null;
    require $filename;
    // Verifie que $infosUser est correctement defini et exploitable
    if (!isset($infosUser) || !is_array($infosUser) || empty($infosUser)) {
        return null;
    }
    return $infosUser;
}

/**
 * Sauvegarde les informations d'un utilisateur dans son fichier associe.
 * Cree le repertoire dataUsers si besoin et ecrit un fichier PHP qui redeclare $infosUser.
 *
 *
 * @param string $username le nom d'utilisateur dont on veut sauvegarder les informations
 * @param array $infosUser le tableau associatif contenant les informations utilisateur
 * @return void
 */
function saveUserInfos($username, $infosUser) {
    // Construit le chemin du fichier utilisateur
    $filename = makeFilenameUser($username);
    // Cree le repertoire dataUsers si il n'existe pas encore
    if (!is_dir('dataUsers')) {
        mkdir('dataUsers', 0755, true);
    }
    // Transforme le tableau PHP en representation textuelle
    $usersPrint = var_export($infosUser, true);
    // Construit le contenu du fichier PHP qui redeclarera $infosUser
    $usersPut = "<?php\n\$infosUser = " . $usersPrint . ";\n?>";
    // Ecrit le contenu dans le fichier utilisateur
    file_put_contents($filename, $usersPut);
}

/**
 * Verifie si il existe deja un fichier utilisateur/compte pour le pseudo passe en parametre
 *
 * @param string $username le nom d'utilisateur
 * @return bool  true si il existe un compte pour  $username, false sinon
 */
function checkAccountAlreadyExists($username){
    // Si username non renseigne ou vide, on considere qu'aucun compte n'existe
    if(!isset($username) || empty(trim($username)))
        return false;
    // On verifie simplement l'existence du fichier associe a ce username
    return file_exists(makeFilenameUser($username));
}

/**
 * Verifie si la connection au compte de nom d'utilisateur est possible via le password
 *
 * @param string $username le nom d'utilisateur a verifier
 * @param string $password le mot de passe a verifier
 * @return array|string  soit la chaine qui decrit le probleme survenu,
 *                        soit le tableau qui contient les booleens de validation
 */
function checkConnection($username, $password) {
    // Verifie la validite des parametres au cas ou
    if(!isset($username) || empty(trim($username)) || !isset($password) || empty($password))
        return "undefined_infos";

    // Charge les infos utilisateur associees a ce username
    $infosUser=loadUserInfos($username);
    if (isset($infosUser) && is_array($infosUser) && !empty($infosUser)) {
        // Retourne un tableau de booleens qui indiquent si username et password sont corrects
        return [
            'username' => checkUsernameFile($username, $infosUser),
            'password' => checkPasswordFile($password, $infosUser)
        ];
    }else{
        // Si le fichier existe mais que les infos sont invalides
        if(checkAccountAlreadyExists($username)){
            return "undefined_infos";
        }else {
            // Si aucun fichier utilisateur pour ce username
            return 'undefined_file';
        }
    }
}

/**
 * Verifie si le nom d'utilisateur est correct dans le tableau d'informations sur l'utilisateur
 * qui, generalement a l'appel de la fonction, vient du fichier d'utilisateur
 *
 * @param string $username le nom d'utilisateur a verifier
 * @param array $infosUser le tableau d'informations sur l'utilisateur
 * @return bool true si le nom d'utilisateur correspond dans le tableau, false sinon
 */
function checkUsernameFile($username, $infosUser){
    // On verifie quand meme si login existe dans le tableau
    return isset($infosUser['username']) &&
        $infosUser['username'] == $username;
}

/**
 * Verifie si le mot de passe est correct dans le tableau d'informations sur l'utilisateur
 * qui, generalement a l'appel de la fonction, vient du fichier d'utilisateur
 *
 * @param string $password le mot de passe a verifier
 * @param array $infosUser le tableau d'informations sur l'utilisateur
 * @return bool true si le mot de passe correspond dans le tableau, false sinon
 */
function checkPasswordFile($password, $infosUser){
    // Utilise password_verify pour comparer le mot de passe en clair et le hash stocke
    return isset($infosUser['password']) &&
        password_verify($password, $infosUser['password']);
}

/**
 * Verifie si le nom de famille correspond a la valeur stockee dans le tableau utilisateur.
 *
 * @param string $lastname le nom de famille a comparer
 * @param array $infosUser le tableau d'informations sur l'utilisateur
 * @return bool true si le nom de famille correspond, false sinon
 */
function checkLastnameFile($lastname, $infosUser){
    // Compare le nom avec la valeur stockee dans le fichier utilisateur
    return isset($infosUser['lastname']) &&
        $lastname == $infosUser['lastname'];
}

/**
 * Verifie si le prenom correspond a la valeur stockee dans le tableau utilisateur.
 *
 * @param string $firstname le prenom a comparer
 * @param array $infosUser le tableau d'informations sur l'utilisateur
 * @return bool true si le prenom correspond, false sinon
 */
function checkFirstnameFile($firstname, $infosUser){
    // Compare le prenom avec la valeur stockee dans le fichier utilisateur
    return isset($infosUser['firstname']) &&
        $firstname == $infosUser['firstname'];
}

/**
 * Verifie si la date de naissance correspond a la valeur stockee dans le tableau utilisateur.
 *
 * @param string $birthdate la date de naissance a comparer
 * @param array $infosUser le tableau d'informations sur l'utilisateur
 * @return bool true si la date de naissance correspond, false sinon
 */
function checkBirthdateFile($birthdate, $infosUser){
    // Compare la date de naissance avec la valeur stockee dans le fichier utilisateur
    return isset($infosUser['birthdate']) &&
        $birthdate == $infosUser['birthdate'];
}

/**
 * Verifie si le gender correspond a la valeur stockee dans le tableau utilisateur.
 *
 * @param string $gender le gender a comparer (male ou female)
 * @param array $infosUser le tableau d'informations sur l'utilisateur
 * @return bool true si le gender correspond, false sinon
 */
function checkGenderFile($gender, $infosUser){
    // Compare le genre avec la valeur stockee dans le fichier utilisateur
    return isset($infosUser['gender']) &&
        $gender == $infosUser['gender'];
}

// Toutes les fonctions de verification de champ, a modifier avec des regex plus strictes
/**
 * Verifie si le nom d'utilisateur est correct par rapport a une regex
 * le nom d'utilisateur provient normalement d'un champ rempli par l'utilisateur
 * On n'accepte pas le nom d'utilisateur vide
 *
 * @param string $username le nom d'utilisateur a verifier
 * @return bool true si le nom d'utilisateur existe, n'est pas vide et passe la regex, false sinon
 */
function checkUsernameField($username) {
    // Verifie que la variable est definie, non vide et ne contient que lettres et chiffres
    return isset($username) && !empty(trim($username)) && preg_match("/^[a-zA-Z0-9]+$/", $username);
}

/**
 * Verifie si le mot de passe est correct par rapport a une regex
 * le mot de passe provient normalement d'un champ rempli par l'utilisateur
 * On n'autorise pas le mot de passe vide.
 *
 * @param string $password le mot de passe a verifier
 * @return bool true si le mot de passe existe, n'est pas vide et passe la regex, false sinon
 */
function checkPasswordField($password) {
    // Mot de passe obligatoire mais sans autres contraintes (pattern tres permissif)
    return isset($password) && !empty(trim($password)) && preg_match("/^.+$/", $password);
}

/**
 * Verifie si le nom est correct par rapport a une regex qui accepte
 * les noms (et prenom) qui sont composes de lettres minuscules et/ou de lettres MAJUSCULES,
 * les caracteres "-", " " et "'".
 * Les lettres peuvent etre accentuees.
 * Tirets et apostrophes sont forcements encadres par deux lettres,
 * Par contre plusieurs espaces sont possibles entre deux parties de prenom/nom.
 * Le nom provient normalement d'un champ rempli par l'utilisateur
 *
 * @param string $lastname le nom a verifier
 * @return bool true si le nom respecte les contraintes ou si le champ est vide, false sinon
 */
function checkLastnameField($lastname){
    // Champ optionnel : si vide ou non defini, on accepte
    return !isset($lastname) || empty(trim($lastname)) || preg_match(
            "/^([a-zA-ZàÀâÂæÆáÁäÄãÃåÅāĀéÉèÈêÊëËęĘėĖēĒÿŸûÛùÙüÜúÚūŪîÎïÏìÌíÍįĮīĪôÔœŒöÖòÒóÓõÕōŌçÇćĆčČñÑńŃ]+(([-']|( )*)[a-zA-ZàÀâÂæÆáÁäÄãÃåÅāĀéÉèÈêÊëËęĘėĖēĒÿŸûÛùÙüÜúÚūŪîÎïÏìÌíÍįĮīĪôÔœŒöÖòÒóÓõÕōŌçÇćĆčČñÑńŃ]+)*)+$/", $lastname);
}

/**
 * Verifie si le prenom est correct par rapport la meme regex que sur le lastname
 * le prenom provient normalement d'un champ rempli par l'utilisateur
 *
 * @param string $firstname le prenom a verifier
 * @return bool true si le prenom respecte les contraintes ou si le champ est vide, false sinon
 */
function checkFirstnameField($firstname){
    // Utilise exactement les memes contraintes que pour le nom
    return checkLastnameField($firstname); // la meme regex et les memes contraintes...
}

/**
 * Verifie si la date de naissance est correct,
 * on verifie si la date de naissance est valide et si aujourd'hui est au moins 18ans apres la naissance
 * la date de naissance provient normalement d'un champ rempli par l'utilisateur
 *
 * @param string $birthdate la date de naissance a verifier au format YYYY-MM-DD
 * @return bool true si la date est valide et l'utilisateur a au moins 18 ans, false sinon
 */
function checkBirthdateField($birthdate){
    // Vide autorise car champ optionnel
    if(!isset($birthdate) || empty(trim($birthdate)))
        return true;

    // Date du jour au format Y-m-d
    $today=date("Y-m-d");
    // On suppose un format YYYY-MM-DD pour la date de naissance
    list($year,$month,$day)=explode('-', $birthdate);

    // On a 18ans lorsque la date est meme jour, meme mois, annee de naissance+18
    $dateOf18Years=($year+18)."-".$month."-".$day; // On a 18ans lorsque la date est
    // meme jour, meme mois, annee de naissance+18
    return (
        // Verifie si la date est valide dans le calendrier
        checkdate($month,$day,$year) &&
        // Verifie que la date des 18 ans est passee ou egale a aujourd'hui
        $dateOf18Years<=$today // Si la date des 18ans est inferieure ou egale a celle du jour
        // c'est que l'utilisateur a plus que 18ans
    );
}

/**
 * Verifie si le gender est correct par rapport a une regex qui match sur 'male' ou 'female'
 * le gender provient normalement d'un champ rempli par l'utilisateur
 *
 * @param string $gender le gender a verifier (male ou female, ou chaine vide)
 * @return bool true si le gender est vide ou valide, false sinon
 */
function checkGenderField($gender){
    // Vide autorise car champ optionnel
    return !isset($gender) || empty(trim($gender)) || preg_match("/^(male|female)$/", $gender);
}


/**
 * Retourne la liste d'un aliment et de toutes ses sous categories dans la hierarchie.
 * Parcourt recursivement la hierarchie a partir de l'aliment donne.
 *
 * @param string $ingredientName le nom de l'ingredient racine dans la hierarchie
 * @param array $hierarchy la hierarchie complete des ingredients et sous categories
 * @return array la liste contenant l'ingredient et toutes ses sous categories
 */
function getIngredientsHierarchy($ingredientName, $hierarchy)
{
    // Liste qui contiendra l'aliment courant et toutes ses sous categories
    $list = array();
    // On commence par ajouter l'aliment de depart
    $list[] = $ingredientName;

    // Si cet aliment a des sous categories dans la hierarchie
    if (isset($hierarchy[$ingredientName]['sous-categorie'])) {
        foreach ($hierarchy[$ingredientName]['sous-categorie'] as $subCat) {
            // Appel recursif pour recuperer toute la branche de la sous categorie
            $subList = getIngredientsHierarchy($subCat, $hierarchy);
            // On fusionne la sous liste dans la liste principale
            foreach ($subList as $element) {
                $list[] = $element;
            }
        }
    }
    // Retourne la liste complete (ingredient + sous categories)
    return $list;
}
?>
